<?php

namespace App\Http\Controllers;

use App\Models\EncabezadoMov;
use App\Models\DetalleMov;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function index()
    {
        return response()->json(
            EncabezadoMov::with(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 4)
                ->orderBy('ID_ENCABEZADO', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'FECHA_ENCA'  => 'required|date',
            'PROVEEDOR'   => 'required|string|max:150',
            'MOTIVO_DEV'  => 'required|string|max:300',
            'detalles'    => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::create([
                'ID_TIPO_MOV'  => 4,
                'FECHA_ENCA'   => $request->FECHA_ENCA,
                'CORRE_AENTRE' => $request->CORRE_AENTRE,
                'PROVEEDOR'    => $request->PROVEEDOR,
                'MOTIVO_DEV'   => $request->MOTIVO_DEV,
                'NGUIA'        => $request->NGUIA,
                'NFACTURA'     => $request->NFACTURA,
                'OBS_ENCA'     => $request->OBS_ENCA,
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
                'Devoluciones',
                'CREAR',
                "Devolución ID {$encabezado->ID_ENCABEZADO} registrada — {$request->PROVEEDOR}",
                $request->ip()
            );

            return response()->json(
                $encabezado->load(['detalles.producto.tipo', 'detalles.producto.marca']),
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'FECHA_ENCA'  => 'required|date',
            'PROVEEDOR'   => 'required|string|max:150',
            'MOTIVO_DEV'  => 'required|string|max:300',
            'detalles'    => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::where('ID_TIPO_MOV', 4)->findOrFail($id);
            $encabezado->update([
                'FECHA_ENCA'   => $request->FECHA_ENCA,
                'CORRE_AENTRE' => $request->CORRE_AENTRE,
                'PROVEEDOR'    => $request->PROVEEDOR,
                'MOTIVO_DEV'   => $request->MOTIVO_DEV,
                'NGUIA'        => $request->NGUIA,
                'NFACTURA'     => $request->NFACTURA,
                'OBS_ENCA'     => $request->OBS_ENCA,
            ]);

            DetalleMov::where('ID_ENCABEZADO', $id)->delete();
            foreach ($request->detalles as $d) {
                DetalleMov::create([
                    'ID_ENCABEZADO' => $id,
                    'ID_PRODUCTO'   => $d['ID_PRODUCTO'],
                    'NSERIEDETA'    => $d['NSERIEDETA'] ?? null,
                    'DETA_CANT'     => $d['DETA_CANT'],
                ]);
            }

            DB::commit();
            Bitacora::registrar('Devoluciones', 'EDITAR', "Devolución ID {$id} modificada", $request->ip());
            return response()->json($encabezado->load(['detalles.producto.tipo', 'detalles.producto.marca']));
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(
            EncabezadoMov::with(['detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 4)
                ->findOrFail($id)
        );
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            DetalleMov::where('ID_ENCABEZADO', $id)->delete();
            EncabezadoMov::where('ID_TIPO_MOV', 4)->findOrFail($id)->delete();
            DB::commit();
            Bitacora::registrar('Devoluciones', 'ELIMINAR', "Devolución ID {$id} eliminada", $request->ip());
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
