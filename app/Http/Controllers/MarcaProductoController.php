<?php

namespace App\Http\Controllers;

use App\Models\MarcaProducto;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class MarcaProductoController extends Controller
{
    public function index()
    {
        return response()->json(MarcaProducto::orderBy('DESCRIPCION_MARCA')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['DESCRIPCION_MARCA' => 'required|string']);
        $marca = MarcaProducto::create($request->only('DESCRIPCION_MARCA'));
        Bitacora::registrar('Marcas', 'CREAR', "Marca '{$marca->DESCRIPCION_MARCA}' creada (ID {$marca->ID_MARCA})", $request->ip());
        return response()->json($marca, 201);
    }

    public function show($id)
    {
        return response()->json(MarcaProducto::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $marca = MarcaProducto::findOrFail($id);
        $marca->update($request->only('DESCRIPCION_MARCA'));
        Bitacora::registrar('Marcas', 'EDITAR', "Marca '{$marca->DESCRIPCION_MARCA}' editada (ID {$id})", $request->ip());
        return response()->json($marca);
    }

    public function destroy(Request $request, $id)
    {
        MarcaProducto::findOrFail($id)->delete();
        Bitacora::registrar('Marcas', 'ELIMINAR', "Marca ID {$id} eliminada", $request->ip());
        return response()->json(null, 204);
    }
}
