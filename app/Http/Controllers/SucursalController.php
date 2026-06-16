<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        return response()->json(Sucursal::orderBy('NOMBRE_SUCURSAL')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['NOMBRE_SUCURSAL' => 'required|string']);
        $sucursal = Sucursal::create($request->only('NOMBRE_SUCURSAL'));
        Bitacora::registrar('Sucursales', 'CREAR', "Sede '{$sucursal->NOMBRE_SUCURSAL}' creada (ID {$sucursal->ID_SUCURSAL})", $request->ip());
        return response()->json($sucursal, 201);
    }

    public function show($id)
    {
        return response()->json(Sucursal::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->update($request->only('NOMBRE_SUCURSAL'));
        Bitacora::registrar('Sucursales', 'EDITAR', "Sede '{$sucursal->NOMBRE_SUCURSAL}' editada (ID {$id})", $request->ip());
        return response()->json($sucursal);
    }

    public function destroy(Request $request, $id)
    {
        Sucursal::findOrFail($id)->delete();
        Bitacora::registrar('Sucursales', 'ELIMINAR', "Sede ID {$id} eliminada", $request->ip());
        return response()->json(null, 204);
    }
}
