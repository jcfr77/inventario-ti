<?php

namespace App\Http\Controllers;

use App\Models\TipoProducto;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class TipoProductoController extends Controller
{
    public function index()
    {
        return response()->json(TipoProducto::orderBy('NOMBRE_TIPO')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['NOMBRE_TIPO' => 'required|string']);
        $tipo = TipoProducto::create($request->only('NOMBRE_TIPO'));
        Bitacora::registrar('Tipos', 'CREAR', "Tipo '{$tipo->NOMBRE_TIPO}' creado (ID {$tipo->ID_TIPO_PRO})", $request->ip());
        return response()->json($tipo, 201);
    }

    public function show($id)
    {
        return response()->json(TipoProducto::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoProducto::findOrFail($id);
        $tipo->update($request->only('NOMBRE_TIPO'));
        Bitacora::registrar('Tipos', 'EDITAR', "Tipo '{$tipo->NOMBRE_TIPO}' editado (ID {$id})", $request->ip());
        return response()->json($tipo);
    }

    public function destroy(Request $request, $id)
    {
        TipoProducto::findOrFail($id)->delete();
        Bitacora::registrar('Tipos', 'ELIMINAR', "Tipo ID {$id} eliminado", $request->ip());
        return response()->json(null, 204);
    }
}
