<?php

namespace App\Http\Controllers;

use App\Models\EncabezadoMov;
use App\Models\DetalleMov;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PrestamoController extends Controller
{
    public function index()
    {
        return response()->json(
            EncabezadoMov::with(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca'])
                ->where('ID_TIPO_MOV', 3)
                ->orderBy('ID_ENCABEZADO', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_SUCURSAL' => 'required|integer',
            'FECHA_ENCA'  => 'required|date',
            'detalles'    => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::create([
                'ID_SUCURSAL'      => $request->ID_SUCURSAL,
                'ID_TIPO_MOV'      => 3,
                'RESPONSABLE'      => $request->RESPONSABLE,
                'OBS_ENCA'         => $request->OBS_ENCA,
                'FECHA_ENCA'       => $request->FECHA_ENCA,
                'FECHA_DEVOLUCION' => $request->FECHA_DEVOLUCION,
                'NPRESTAMO'        => $request->NPRESTAMO,
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
            Bitacora::registrar('Préstamos', 'CREAR', "Préstamo ID {$encabezado->ID_ENCABEZADO} registrado — " . count($request->detalles) . " producto(s)", $request->ip());

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
                ->where('ID_TIPO_MOV', 3)->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ID_SUCURSAL' => 'required|integer',
            'FECHA_ENCA'  => 'required|date',
            'detalles'    => 'required|array|min:1',
            'detalles.*.ID_PRODUCTO' => 'required|integer',
            'detalles.*.DETA_CANT'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $encabezado = EncabezadoMov::where('ID_TIPO_MOV', 3)->findOrFail($id);
            $encabezado->update([
                'ID_SUCURSAL'      => $request->ID_SUCURSAL,
                'RESPONSABLE'      => $request->RESPONSABLE,
                'OBS_ENCA'         => $request->OBS_ENCA,
                'FECHA_ENCA'       => $request->FECHA_ENCA,
                'FECHA_DEVOLUCION' => $request->FECHA_DEVOLUCION,
                'NPRESTAMO'        => $request->NPRESTAMO,
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
            Bitacora::registrar('Préstamos', 'EDITAR', "Préstamo ID {$id} modificado", $request->ip());

            return response()->json($encabezado->load(['sucursal', 'detalles.producto.tipo', 'detalles.producto.marca']));
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            DetalleMov::where('ID_ENCABEZADO', $id)->delete();
            EncabezadoMov::where('ID_TIPO_MOV', 3)->findOrFail($id)->delete();
            DB::commit();
            Bitacora::registrar('Préstamos', 'ELIMINAR', "Préstamo ID {$id} eliminado", $request->ip());
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function subirArchivo(Request $request, $id)
    {
        $request->validate(['archivo' => 'required|file|max:10240']);
        $prestamo = EncabezadoMov::where('ID_TIPO_MOV', 3)->findOrFail($id);
        if ($prestamo->ARCHIVO) {
            Storage::disk('public')->delete($prestamo->ARCHIVO);
        }
        $path = $request->file('archivo')->store('prestamos', 'public');
        $prestamo->update(['ARCHIVO' => $path]);
        return response()->json(['path' => $path]);
    }

    public function descargarArchivo($id)
    {
        $prestamo = EncabezadoMov::where('ID_TIPO_MOV', 3)->findOrFail($id);
        if (!$prestamo->ARCHIVO || !Storage::disk('public')->exists($prestamo->ARCHIVO)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
        return Storage::disk('public')->download($prestamo->ARCHIVO);
    }
}
