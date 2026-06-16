<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        return response()->json(Rol::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'NOMBRE_ROL'  => 'required|string|max:50|unique:infra_rol,NOMBRE_ROL',
            'DESCRIPCION' => 'nullable|string|max:150',
        ]);
        return response()->json(Rol::create($request->only('NOMBRE_ROL', 'DESCRIPCION')), 201);
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $request->validate([
            'NOMBRE_ROL'  => 'required|string|max:50|unique:infra_rol,NOMBRE_ROL,' . $id . ',ID_ROL',
            'DESCRIPCION' => 'nullable|string|max:150',
        ]);
        $rol->update($request->only('NOMBRE_ROL', 'DESCRIPCION'));
        return response()->json($rol);
    }

    public function permisos()
    {
        return response()->json(
            Permiso::orderBy('ORDEN')->get(['ID_PERMISO','CLAVE','ETIQUETA','ICONO','GRUPO'])
        );
    }

    public function asignarPermisos(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $ids = $request->input('permisos', []);
        $rol->permisos()->sync($ids);
        return response()->json(['message' => 'Permisos actualizados']);
    }

    public function permisosRol($id)
    {
        $rol = Rol::with('permisos')->findOrFail($id);
        return response()->json($rol->permisos->pluck('ID_PERMISO'));
    }

    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);
        if ($rol->ID_ROL <= 3) {
            return response()->json(['error' => 'No se pueden eliminar los roles del sistema'], 422);
        }
        $rol->delete();
        return response()->json(null, 204);
    }
}
