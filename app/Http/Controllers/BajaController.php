<?php

namespace App\Http\Controllers;

use App\Models\EncabezadoMov;
use App\Models\DetalleMov;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BajaController extends Controller
{
    public function index()
    {
        return response()->json(
            EncabezadoMov::with(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 5)
                ->orderBy('ID_ENCABEZADO', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_SUCURSAL' => 'required|integer',
            'FECHA_ENCA'  => 'required|date',
            'MOTIVO_BAJA' => 'required|string|max:100',
            'detalles'    => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::create([
                'ID_TIPO_MOV'  => 5,
                'ID_SUCURSAL'  => $request->ID_SUCURSAL,
                'FECHA_ENCA'   => $request->FECHA_ENCA,
                'CORRE_AENTRE' => $request->CORRE_AENTRE,
                'MOTIVO_BAJA'  => $request->MOTIVO_BAJA,
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
            Bitacora::registrar('Bajas', 'CREAR', "Baja ID {$encabezado->ID_ENCABEZADO} registrada — {$request->MOTIVO_BAJA}", $request->ip());
            return response()->json($encabezado->load(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca']), 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ID_SUCURSAL' => 'required|integer',
            'FECHA_ENCA'  => 'required|date',
            'MOTIVO_BAJA' => 'required|string|max:100',
            'detalles'    => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::where('ID_TIPO_MOV', 5)->findOrFail($id);
            $encabezado->update([
                'ID_SUCURSAL'  => $request->ID_SUCURSAL,
                'FECHA_ENCA'   => $request->FECHA_ENCA,
                'CORRE_AENTRE' => $request->CORRE_AENTRE,
                'MOTIVO_BAJA'  => $request->MOTIVO_BAJA,
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
            Bitacora::registrar('Bajas', 'EDITAR', "Baja ID {$id} modificada", $request->ip());
            return response()->json($encabezado->load(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca']));
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(
            EncabezadoMov::with(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 5)
                ->findOrFail($id)
        );
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            DetalleMov::where('ID_ENCABEZADO', $id)->delete();
            EncabezadoMov::where('ID_TIPO_MOV', 5)->findOrFail($id)->delete();
            DB::commit();
            Bitacora::registrar('Bajas', 'ELIMINAR', "Baja ID {$id} eliminada", $request->ip());
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function subirArchivo(Request $request, $id)
    {
        $request->validate(['archivo' => 'required|file|max:10240']);
        $baja = EncabezadoMov::where('ID_TIPO_MOV', 5)->findOrFail($id);

        if ($baja->ARCHIVO && Storage::disk('public')->exists($baja->ARCHIVO)) {
            Storage::disk('public')->delete($baja->ARCHIVO);
        }

        $path = $request->file('archivo')->store('bajas', 'public');
        $baja->update(['ARCHIVO' => $path]);

        Bitacora::registrar('Bajas', 'ARCHIVO', "Foto adjunta a la Baja ID {$id}", $request->ip());
        return response()->json(['archivo' => $path]);
    }

    public function descargarArchivo($id)
    {
        $baja = EncabezadoMov::where('ID_TIPO_MOV', 5)->findOrFail($id);
        if (!$baja->ARCHIVO || !Storage::disk('public')->exists($baja->ARCHIVO)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
        return Storage::disk('public')->download($baja->ARCHIVO);
    }
}
