<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return response()->json(
            Producto::with(['tipo', 'marca', 'movimientoActual.sucursal', 'movimientoActual.estado'])->orderBy('MODELO_PRO')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_TIPO_PRO' => 'required|integer',
            'ID_MARCA'    => 'nullable|integer',
            'MODELO_PRO'  => 'required|string',
            'PROCESADOR'  => 'nullable|string',
            'RAM'         => 'nullable|string',
        ]);

        $producto = Producto::create($request->only(['ID_TIPO_PRO', 'ID_MARCA', 'MODELO_PRO', 'PROCESADOR', 'RAM']));
        Bitacora::registrar('Productos', 'CREAR', "Producto '{$producto->MODELO_PRO}' creado (ID {$producto->ID_PRODUCTO})", $request->ip());
        return response()->json($producto, 201);
    }

    public function show($id)
    {
        return response()->json(
            Producto::with(['tipo', 'marca', 'movimientos.sucursal', 'movimientos.estado'])->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update($request->only(['ID_TIPO_PRO', 'ID_MARCA', 'MODELO_PRO', 'PROCESADOR', 'RAM']));
        Bitacora::registrar('Productos', 'EDITAR', "Producto '{$producto->MODELO_PRO}' editado (ID {$id})", $request->ip());
        return response()->json($producto);
    }

    public function destroy(Request $request, $id)
    {
        Producto::findOrFail($id)->delete();
        Bitacora::registrar('Productos', 'ELIMINAR', "Producto ID {$id} eliminado", $request->ip());
        return response()->json(null, 204);
    }
}
