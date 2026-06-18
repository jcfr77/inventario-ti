<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(
            Usuario::with('rol')->orderBy('ID_USUARIO')->get()
                ->map(fn($u) => [
                    'ID_USUARIO' => $u->ID_USUARIO,
                    'NOMBRE'     => $u->NOMBRE,
                    'EMAIL'      => $u->EMAIL,
                    'ID_ROL'     => $u->ID_ROL,
                    'ROL'        => $u->rol->NOMBRE_ROL ?? '—',
                    'ACTIVO'     => $u->ACTIVO,
                    'CREATED_AT' => $u->CREATED_AT,
                ])
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'NOMBRE'   => 'required|string|max:100',
            'EMAIL'    => 'required|email|unique:infra_usuario,EMAIL',
            'PASSWORD' => 'required|string|min:6',
            'ID_ROL'   => 'required|integer|exists:infra_rol,ID_ROL',
        ]);

        $usuario = Usuario::create([
            'NOMBRE'   => $request->NOMBRE,
            'EMAIL'    => $request->EMAIL,
            'PASSWORD' => Hash::make($request->PASSWORD),
            'ID_ROL'   => $request->ID_ROL,
            'ACTIVO'   => 1,
            'DEBE_CAMBIAR_PASSWORD' => 1,
        ]);

        return response()->json($usuario->load('rol'), 201);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'NOMBRE' => 'required|string|max:100',
            'EMAIL'  => 'required|email|unique:infra_usuario,EMAIL,' . $id . ',ID_USUARIO',
            'ID_ROL' => 'required|integer|exists:infra_rol,ID_ROL',
            'ACTIVO' => 'required|boolean',
        ]);

        $datos = [
            'NOMBRE' => $request->NOMBRE,
            'EMAIL'  => $request->EMAIL,
            'ID_ROL' => $request->ID_ROL,
            'ACTIVO' => $request->ACTIVO,
        ];

        if ($request->filled('PASSWORD')) {
            $request->validate(['PASSWORD' => 'string|min:6']);
            $datos['PASSWORD'] = Hash::make($request->PASSWORD);
            $datos['DEBE_CAMBIAR_PASSWORD'] = 1;
        }

        $usuario->update($datos);
        return response()->json($usuario->load('rol'));
    }

    public function destroy(Request $request, $id)
    {
        if ((int)$id === $request->user()->ID_USUARIO) {
            return response()->json(['error' => 'No puedes eliminar tu propio usuario'], 422);
        }
        Usuario::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function sucursales($id)
    {
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario->sucursales()->pluck('infra_sucursal.ID_SUCURSAL'));
    }

    public function asignarSucursales(Request $request, $id)
    {
        $request->validate(['sucursales' => 'present|array', 'sucursales.*' => 'integer']);
        $usuario = Usuario::findOrFail($id);
        $usuario->sucursales()->sync($request->sucursales);
        return response()->json(['sucursales' => $usuario->sucursales()->pluck('ID_SUCURSAL')]);
    }
}
