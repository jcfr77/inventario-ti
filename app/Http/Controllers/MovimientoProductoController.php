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
                ->where('VIGENTE', 1)
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

    public function historial()
    {
        return response()->json(
            MovimientoProducto::with(['sucursal', 'estado', 'producto.tipo', 'producto.marca'])
                ->where('ID_ESTADO', 4)
                ->orderBy('ID_MOVIMIENTO', 'desc')
                ->get()
        );
    }

    public function traslado(Request $request)
    {
        $request->validate([
            'ids'               => 'required|array|min:1',
            'ids.*'             => 'integer',
            'ID_SUCURSAL'       => 'nullable|integer',
            'UBICACION_DESTINO' => 'required|string|max:255',
            'FECHA_TRASLADO'    => 'nullable|date',
            'OBS_TRASLADO'      => 'nullable|string|max:500',
        ]);

        $activos = MovimientoProducto::whereIn('ID_MOVIMIENTO', $request->ids)->get();

        foreach ($activos as $activo) {
            $activo->update(['VIGENTE' => 0]);

            MovimientoProducto::create([
                'ID_SUCURSAL'   => $request->ID_SUCURSAL ?? $activo->ID_SUCURSAL,
                'ID_ESTADO'     => 4,
                'ID_PRODUCTO'   => $activo->ID_PRODUCTO,
                'NSERIE_PRO'    => $activo->NSERIE_PRO,
                'MAC_PRODUCTO'  => $activo->MAC_PRODUCTO,
                'IP_INTERNA'    => $activo->IP_INTERNA,
                'UBICACION_PRO' => $request->UBICACION_DESTINO,
                'OBS_BAJA'      => $activo->OBS_BAJA,
                'FECHA_INGRESO' => now()->toDateString(),
                'VIGENTE'       => 1,
            ]);
        }

        $cantidad = count($request->ids);
        Bitacora::registrar('infra_movimientos_productos', 'TRASLADO', "Traslado: {$cantidad} activo(s) → {$request->UBICACION_DESTINO}", $request->ip());

        return response()->json(['trasladados' => $cantidad]);
    }

    public function destroyHistorial(Request $request, $id)
    {
        $movimiento = MovimientoProducto::findOrFail($id);

        $query = MovimientoProducto::where('ID_PRODUCTO', $movimiento->ID_PRODUCTO)
            ->where('VIGENTE', 0)
            ->where('ID_MOVIMIENTO', '<', $id)
            ->orderBy('ID_MOVIMIENTO', 'desc');

        if ($movimiento->NSERIE_PRO) {
            $query->where('NSERIE_PRO', $movimiento->NSERIE_PRO);
        } else {
            $query->whereNull('NSERIE_PRO');
        }

        $anterior = $query->first();

        $movimiento->delete();

        if ($anterior) {
            $anterior->update(['VIGENTE' => 1]);
        }

        Bitacora::registrar('infra_movimientos_productos', 'ELIMINAR', "Historial #{$id} eliminado" . ($anterior ? ", restaurado ID {$anterior->ID_MOVIMIENTO}" : ''), $request->ip());
        return response()->json(null, 204);
    }

    public function destroy(Request $request, $id)
    {
        MovimientoProducto::findOrFail($id)->delete();
        Bitacora::registrar('Movimientos', 'ELIMINAR', "Movimiento ID {$id} eliminado", $request->ip());
        return response()->json(null, 204);
    }
}
