<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        return response()->json(Estado::orderBy('NOMBRE_ESTADO')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['NOMBRE_ESTADO' => 'required|string']);
        $estado = Estado::create($request->only('NOMBRE_ESTADO'));
        return response()->json($estado, 201);
    }

    public function show($id)
    {
        return response()->json(Estado::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);
        $estado->update($request->only('NOMBRE_ESTADO'));
        return response()->json($estado);
    }

    public function destroy($id)
    {
        Estado::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
