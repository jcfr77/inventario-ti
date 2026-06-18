<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MovimientoProducto;
use App\Models\DetalleMov;
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

    private function buildTimeline(int $id, ?string $nserie = null): array
    {
        $tiposStock = [1 => 'INGRESO', 2 => 'EGRESO', 3 => 'PRÉSTAMO', 4 => 'DEVOLUCIÓN', 5 => 'BAJA'];

        $baseStock = DB::table('infra_detalle_mov as d')
            ->join('infra_encabezado_mov as e', 'e.ID_ENCABEZADO', '=', 'd.ID_ENCABEZADO')
            ->leftJoin('infra_sucursal as s', 's.ID_SUCURSAL', '=', 'e.ID_SUCURSAL')
            ->where('d.ID_PRODUCTO', $id);

        if ($nserie !== null) {
            // Si el serial está registrado en NSERIEDETA, filtrar por él.
            // Si no, mostrar todos los movimientos de stock del producto (contexto de lote).
            $tieneEnStock = (clone $baseStock)->where('d.NSERIEDETA', $nserie)->exists();
            if ($tieneEnStock) {
                $baseStock->where('d.NSERIEDETA', $nserie);
            }
        }

        $stockMovs = $baseStock
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

        $activoQ = MovimientoProducto::with(['sucursal', 'estado'])
            ->where('ID_PRODUCTO', $id);

        if ($nserie !== null) {
            $activoQ->where('NSERIE_PRO', $nserie);
        }

        $activoMovs = $activoQ
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

        return $stockMovs->concat($activoMovs)->sortBy('fecha')->values()->all();
    }

    public function unidades($id)
    {
        Producto::findOrFail($id);

        // Unidades registradas en activos (con o sin serial)
        $activos = MovimientoProducto::with(['sucursal', 'estado'])
            ->where('ID_PRODUCTO', $id)
            ->where('VIGENTE', 1)
            ->get()
            ->map(fn($m) => [
                'id'       => $m->ID_MOVIMIENTO,
                'serie'    => $m->NSERIE_PRO,
                'estado'   => $m->estado->NOMBRE_ESTADO ?? '—',
                'sede'     => $m->sucursal->NOMBRE_SUCURSAL ?? '—',
                'ubicacion'=> $m->UBICACION_PRO,
                'fuente'   => 'activo',
            ]);

        // Seriales en stock (NSERIEDETA) que no están en activos
        $seriesActivos = $activos->pluck('serie')->filter()->values();
        $stock = DB::table('infra_detalle_mov as d')
            ->join('infra_encabezado_mov as e', 'e.ID_ENCABEZADO', '=', 'd.ID_ENCABEZADO')
            ->leftJoin('infra_sucursal as s', 's.ID_SUCURSAL', '=', 'e.ID_SUCURSAL')
            ->where('d.ID_PRODUCTO', $id)
            ->whereNotNull('d.NSERIEDETA')
            ->when($seriesActivos->isNotEmpty(), fn($q) => $q->whereNotIn('d.NSERIEDETA', $seriesActivos))
            ->select('d.NSERIEDETA as serie', 's.NOMBRE_SUCURSAL as sede', 'e.FECHA_ENCA as fecha')
            ->orderByDesc('e.FECHA_ENCA')
            ->get()
            ->unique('serie')
            ->map(fn($r) => [
                'id'       => null,
                'serie'    => $r->serie,
                'estado'   => 'EN STOCK',
                'sede'     => $r->sede ?? '—',
                'ubicacion'=> null,
                'fuente'   => 'stock',
            ]);

        return response()->json($activos->concat($stock)->values());
    }

    public function trazabilidad($id)
    {
        Producto::findOrFail($id);
        return response()->json($this->buildTimeline((int)$id));
    }

    public function trazabilidadSerie($nserie)
    {
        $idProducto = null;

        $mov = MovimientoProducto::where('NSERIE_PRO', $nserie)->first();
        if ($mov) $idProducto = $mov->ID_PRODUCTO;

        if (!$idProducto) {
            $det = DetalleMov::where('NSERIEDETA', $nserie)->first();
            if ($det) $idProducto = $det->ID_PRODUCTO;
        }

        if (!$idProducto) {
            return response()->json(['error' => "N° serie '{$nserie}' no encontrado"], 404);
        }

        $producto = Producto::with(['tipo', 'marca'])->find($idProducto);

        return response()->json([
            'producto' => $producto,
            'timeline' => $this->buildTimeline($idProducto, $nserie),
        ]);
    }
}
