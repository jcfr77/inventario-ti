<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index()
    {
        return response()->json(
            Bitacora::with('usuario')->orderBy('ID_LOG', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        Bitacora::registrar(
            $request->input('tabla', 'Informes'),
            $request->input('accion', 'EXPORTAR'),
            $request->input('descripcion', ''),
            $request->ip()
        );
        return response()->json(null, 201);
    }

    public function destroy($id)
    {
        Bitacora::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
