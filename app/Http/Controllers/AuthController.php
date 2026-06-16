<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::with('rol')->where('EMAIL', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->PASSWORD)) {
            Bitacora::registrar('Auth', 'LOGIN_FALLIDO', "Intento fallido de inicio de sesión: {$request->email}", $request->ip());
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (!$usuario->ACTIVO) {
            Bitacora::registrar('Auth', 'LOGIN_FALLIDO', "Intento de inicio de sesión de usuario desactivado: {$usuario->EMAIL}", $request->ip(), $usuario->ID_USUARIO);
            return response()->json(['message' => 'Usuario desactivado'], 403);
        }

        $token    = $usuario->createToken('api-token')->plainTextToken;
        $permisos = $usuario->rol->permisos()->orderBy('ORDEN')->get(['CLAVE','ETIQUETA','ICONO','GRUPO']);

        Bitacora::registrar('Auth', 'LOGIN', "Inicio de sesión: {$usuario->NOMBRE}", $request->ip(), $usuario->ID_USUARIO);

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'                    => $usuario->ID_USUARIO,
                'nombre'                => $usuario->NOMBRE,
                'email'                 => $usuario->EMAIL,
                'rol'                   => $usuario->rol->NOMBRE_ROL,
                'id_rol'                => $usuario->ID_ROL,
                'permisos'              => $permisos->pluck('CLAVE'),
                'debe_cambiar_password' => $usuario->DEBE_CAMBIAR_PASSWORD,
            ],
        ]);
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required|string',
            'password_nueva'  => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $usuario = $request->user();

        if (!Hash::check($request->password_actual, $usuario->PASSWORD)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 422);
        }

        $usuario->update([
            'PASSWORD'              => Hash::make($request->password_nueva),
            'DEBE_CAMBIAR_PASSWORD' => 0,
        ]);

        Bitacora::registrar('Auth', 'CAMBIO_PASSWORD', "Cambio de contraseña: {$usuario->NOMBRE}", $request->ip(), $usuario->ID_USUARIO);

        return response()->json(['message' => 'Contraseña actualizada correctamente']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function me(Request $request)
    {
        $usuario  = $request->user()->load('rol');
        $permisos = $usuario->rol->permisos()->orderBy('ORDEN')->pluck('CLAVE');
        return response()->json([
            'id'                    => $usuario->ID_USUARIO,
            'nombre'                => $usuario->NOMBRE,
            'email'                 => $usuario->EMAIL,
            'rol'                   => $usuario->rol->NOMBRE_ROL,
            'id_rol'                => $usuario->ID_ROL,
            'permisos'              => $permisos,
            'debe_cambiar_password' => $usuario->DEBE_CAMBIAR_PASSWORD,
        ]);
    }
}
