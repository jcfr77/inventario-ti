<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MovimientoProducto;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function trazabilidad($id)
    {
        Producto::findOrFail($id);

        $tiposStock = [1 => 'INGRESO', 2 => 'EGRESO', 3 => 'PRÉSTAMO', 4 => 'DEVOLUCIÓN', 5 => 'BAJA'];

        $stockMovs = DB::table('infra_detalle_mov as d')
            ->join('infra_encabezado_mov as e', 'e.ID_ENCABEZADO', '=', 'd.ID_ENCABEZADO')
            ->leftJoin('infra_sucursal as s', 's.ID_SUCURSAL', '=', 'e.ID_SUCURSAL')
            ->where('d.ID_PRODUCTO', $id)
            ->select('e.FECHA_ENCA as fecha', 'e.ID_TIPO_MOV', 'd.DETA_CANT as cantidad',
                     'd.NSERIEDETA as serie', 's.NOMBRE_SUCURSAL as sede',
                     'e.OBS_ENCA as obs', 'e.RESPONSABLE')
            ->orderBy('e.FECHA_ENCA')
            ->get()
            ->map(fn($r) => [
                'fecha'   => $r->fecha,
                'tipo'    => $tiposStock[$r->ID_TIPO_MOV] ?? 'MOVIMIENTO',
                'cantidad'=> (int)$r->cantidad,
                'serie'   => $r->serie,
                'sede'    => $r->sede ?? '—',
                'detalle' => implode(' · ', array_filter([$r->RESPONSABLE, $r->obs])),
                'fuente'  => 'stock',
                'vigente' => null,
            ]);

        $activoMovs = MovimientoProducto::with(['sucursal', 'estado'])
            ->where('ID_PRODUCTO', $id)
            ->orderBy('FECHA_INGRESO')
            ->get()
            ->map(fn($m) => [
                'fecha'   => $m->FECHA_INGRESO,
                'tipo'    => $m->estado->NOMBRE_ESTADO ?? 'ACTIVO',
                'cantidad'=> 1,
                'serie'   => $m->NSERIE_PRO,
                'sede'    => $m->sucursal->NOMBRE_SUCURSAL ?? '—',
                'detalle' => implode(' · ', array_filter([$m->UBICACION_PRO, $m->OBS_BAJA])),
                'fuente'  => 'activo',
                'vigente' => $m->VIGENTE,
            ]);

        return response()->json(
            $stockMovs->concat($activoMovs)->sortBy('fecha')->values()
        );
    }
}
