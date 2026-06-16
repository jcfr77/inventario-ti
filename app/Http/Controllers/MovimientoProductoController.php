<?php

namespace App\Http\Controllers;

use App\Models\MovimientoProducto;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class MovimientoProductoController extends Controller
{
    public function index()
    {
        return response()->json(
            MovimientoProducto::with(['sucursal', 'estado', 'producto.tipo', 'producto.marca'])
                ->orderBy('ID_MOVIMIENTO', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_SUCURSAL'  => 'required|integer',
            'ID_ESTADO'    => 'required|integer',
            'ID_PRODUCTO'  => 'required|integer',
            'NSERIE_PRO'   => 'nullable|string',
            'MAC_PRODUCTO' => 'nullable|string',
            'IP_INTERNA'   => 'nullable|string',
            'UBICACION_PRO'=> 'nullable|string',
            'OBS_BAJA'     => 'nullable|string',
        ]);

        $movimiento = MovimientoProducto::create(array_merge(
            $request->only(['ID_SUCURSAL', 'ID_ESTADO', 'ID_PRODUCTO',
                'NSERIE_PRO', 'MAC_PRODUCTO', 'IP_INTERNA', 'UBICACION_PRO', 'OBS_BAJA']),
            ['FECHA_INGRESO' => now()]
        ));

        Bitacora::registrar('Movimientos', 'CREAR', "Movimiento ID {$movimiento->ID_MOVIMIENTO} creado - Producto ID {$movimiento->ID_PRODUCTO}", $request->ip());
        return response()->json($movimiento->load(['sucursal', 'estado', 'producto']), 201);
    }

    public function show($id)
    {
        return response()->json(
            MovimientoProducto::with(['sucursal', 'estado', 'producto.tipo', 'producto.marca'])->findOrFail($id)
        );
    }

    public function porProducto($idProducto)
    {
        return response()->json(
            MovimientoProducto::with(['sucursal', 'estado'])
                ->where('ID_PRODUCTO', $idProducto)
                ->get()
        );
    }

    public function update(Request $request, $id)
    {
        $movimiento = MovimientoProducto::findOrFail($id);
        $movimiento->update($request->only([
            'ID_SUCURSAL', 'ID_ESTADO', 'ID_PRODUCTO',
            'NSERIE_PRO', 'MAC_PRODUCTO', 'IP_INTERNA', 'UBICACION_PRO', 'OBS_BAJA',
        ]));
        Bitacora::registrar('Movimientos', 'EDITAR', "Movimiento ID {$id} actualizado", $request->ip());
        return response()->json($movimiento);
    }

    public function destroy(Request $request, $id)
    {
        MovimientoProducto::findOrFail($id)->delete();
        Bitacora::registrar('Movimientos', 'ELIMINAR', "Movimiento ID {$id} eliminado", $request->ip());
        return response()->json(null, 204);
    }
}
