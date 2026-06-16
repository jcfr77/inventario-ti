<?php

namespace App\Http\Controllers;

use App\Models\EncabezadoMov;
use App\Models\DetalleMov;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EgresoController extends Controller
{
    public function stock()
    {
        $rows = DB::table('infra_detalle_mov as d')
            ->join('infra_encabezado_mov as e', 'e.ID_ENCABEZADO', '=', 'd.ID_ENCABEZADO')
            ->join('infra_producto as p', 'p.ID_PRODUCTO', '=', 'd.ID_PRODUCTO')
            ->leftJoin('infra_tipo_producto as t', 't.ID_TIPO_PRO', '=', 'p.ID_TIPO_PRO')
            ->leftJoin('infra_marca_producto as m', 'm.ID_MARCA', '=', 'p.ID_MARCA')
            ->select(
                'p.ID_PRODUCTO',
                'p.MODELO_PRO',
                't.NOMBRE_TIPO',
                'm.DESCRIPCION_MARCA',
                DB::raw("SUM(CASE WHEN e.ID_TIPO_MOV = 1 THEN d.DETA_CANT ELSE -d.DETA_CANT END) as STOCK")
            )
            ->groupBy('p.ID_PRODUCTO', 'p.MODELO_PRO', 't.NOMBRE_TIPO', 'm.DESCRIPCION_MARCA')
            ->having('STOCK', '>', 0)
            ->orderBy('t.NOMBRE_TIPO')
            ->orderBy('m.DESCRIPCION_MARCA')
            ->orderBy('p.MODELO_PRO')
            ->get();

        // Series ingresadas agrupadas por producto
        $ingresadas = DB::table('infra_detalle_mov as d')
            ->join('infra_encabezado_mov as e', 'e.ID_ENCABEZADO', '=', 'd.ID_ENCABEZADO')
            ->where('e.ID_TIPO_MOV', 1)
            ->whereNotNull('d.NSERIEDETA')
            ->where('d.NSERIEDETA', '!=', '')
            ->select('d.ID_PRODUCTO', 'd.NSERIEDETA')
            ->get()
            ->groupBy('ID_PRODUCTO');

        // Series ya egresadas (para excluirlas)
        $egresadas = DB::table('infra_detalle_mov as d')
            ->join('infra_encabezado_mov as e', 'e.ID_ENCABEZADO', '=', 'd.ID_ENCABEZADO')
            ->where('e.ID_TIPO_MOV', 2)
            ->whereNotNull('d.NSERIEDETA')
            ->where('d.NSERIEDETA', '!=', '')
            ->select('d.ID_PRODUCTO', 'd.NSERIEDETA')
            ->get()
            ->groupBy('ID_PRODUCTO')
            ->map(fn($items) => $items->pluck('NSERIEDETA')->all());

        $resultado = $rows->map(function ($r) use ($ingresadas, $egresadas) {
            $idProd   = $r->ID_PRODUCTO;
            $usadas   = $egresadas[$idProd] ?? [];
            $series   = isset($ingresadas[$idProd])
                ? $ingresadas[$idProd]->pluck('NSERIEDETA')->filter(fn($s) => !in_array($s, $usadas))->values()->all()
                : [];

            return [
                'ID_PRODUCTO'      => $idProd,
                'MODELO_PRO'       => $r->MODELO_PRO,
                'STOCK'            => (int) $r->STOCK,
                'series'           => $series,
                'tipo'             => ['NOMBRE_TIPO'       => $r->NOMBRE_TIPO],
                'marca'            => ['DESCRIPCION_MARCA' => $r->DESCRIPCION_MARCA],
            ];
        });

        return response()->json($resultado);
    }

    public function index()
    {
        return response()->json(
            EncabezadoMov::with(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 2)
                ->orderBy('ID_ENCABEZADO', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_SUCURSAL'   => 'required|integer',
            'FECHA_ENCA'    => 'required|date',
            'detalles'      => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::create([
                'ID_SUCURSAL'   => $request->ID_SUCURSAL,
                'ID_TIPO_MOV'   => 2,
                'OCOMPRA'       => $request->OCOMPRA,
                'NFACTURA'      => $request->NFACTURA,
                'NGUIA'         => $request->NGUIA,
                'OBS_ENCA'      => $request->OBS_ENCA,
                'FECHA_ENCA'    => $request->FECHA_ENCA,
                'FECHA_FACTURA' => $request->FECHA_FACTURA,
                'TOTAL_FACTURA' => $request->TOTAL_FACTURA,
                'CORRE_AENTRE'  => $request->CORRE_AENTRE,
            ]);

            foreach ($request->detalles as $d) {
                DetalleMov::create([
                    'ID_ENCABEZADO' => $encabezado->ID_ENCABEZADO,
                    'ID_PRODUCTO'   => $d['ID_PRODUCTO'],
                    'NSERIEDETA'    => $d['NSERIEDETA'] ?? null,
                    'DETA_CANT'     => $d['DETA_CANT'],
                ]);
            }

            DB::commit();

            Bitacora::registrar(
                'Egresos',
                'CREAR',
                "Egreso ID {$encabezado->ID_ENCABEZADO} registrado — " . count($request->detalles) . " producto(s)",
                $request->ip()
            );

            return response()->json(
                $encabezado->load(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca']),
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(
            EncabezadoMov::with(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 2)
                ->findOrFail($id)
        );
    }

    public function subirArchivo(Request $request, $id)
    {
        $request->validate(['archivo' => 'required|file|max:10240']);
        $egreso = EncabezadoMov::where('ID_TIPO_MOV', 2)->findOrFail($id);

        if ($egreso->ARCHIVO && Storage::disk('public')->exists($egreso->ARCHIVO)) {
            Storage::disk('public')->delete($egreso->ARCHIVO);
        }

        $path = $request->file('archivo')->store('egresos', 'public');
        $egreso->update(['ARCHIVO' => $path]);

        Bitacora::registrar('Egresos', 'ARCHIVO', "Archivo adjunto al Egreso ID {$id}", $request->ip());
        return response()->json(['archivo' => $path]);
    }

    public function descargarArchivo($id)
    {
        $egreso = EncabezadoMov::where('ID_TIPO_MOV', 2)->findOrFail($id);
        if (!$egreso->ARCHIVO || !Storage::disk('public')->exists($egreso->ARCHIVO)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
        return Storage::disk('public')->download($egreso->ARCHIVO);
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            DetalleMov::where('ID_ENCABEZADO', $id)->delete();
            EncabezadoMov::findOrFail($id)->delete();
            DB::commit();
            Bitacora::registrar('Egresos', 'ELIMINAR', "Egreso ID {$id} eliminado", $request->ip());
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
