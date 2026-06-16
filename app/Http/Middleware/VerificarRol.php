<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarRol
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $usuario = $request->user();
        if (!$usuario || !in_array($usuario->ID_ROL, array_map('intval', $roles))) {
            return response()->json(['error' => 'No tiene permisos para esta acción'], 403);
        }
        return $next($request);
    }
}
