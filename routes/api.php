<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\MarcaProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TipoProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MovimientoProductoController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\MikroTikController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\BajaController;

// Rutas públicas
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',     [AuthController::class, 'me']);
    Route::put('auth/password', [AuthController::class, 'cambiarPassword']);

    // Solo Administrador (rol 1)
    Route::middleware('rol:1')->group(function () {
        Route::apiResource('usuarios', UsuarioController::class)->only(['index','store','update','destroy']);
        Route::apiResource('roles', RolController::class)->only(['index','store','update','destroy']);
        Route::get('permisos',                    [RolController::class, 'permisos']);
        Route::get('roles/{id}/permisos',         [RolController::class, 'permisosRol']);
        Route::post('roles/{id}/permisos',        [RolController::class, 'asignarPermisos']);
    });

    // Administrador + Técnico TI (roles 1 y 2) — escritura
    Route::middleware('rol:1,2')->group(function () {
        Route::apiResource('sucursales', SucursalController::class)->only(['store','update','destroy']);
        Route::apiResource('tipos-producto', TipoProductoController::class)->only(['store','update','destroy']);
        Route::apiResource('marcas', MarcaProductoController::class)->only(['store','update','destroy']);
        Route::apiResource('productos', ProductoController::class)->only(['store','update','destroy']);
        Route::apiResource('ingresos', IngresoController::class)->only(['store','update','destroy']);
        Route::post('egresos/{id}/archivo',   [EgresoController::class, 'subirArchivo']);
        Route::apiResource('egresos', EgresoController::class)->only(['store','destroy']);
        Route::post('prestamos/{id}/archivo', [PrestamoController::class, 'subirArchivo']);
        Route::apiResource('prestamos', PrestamoController::class)->only(['store','update','destroy']);
        Route::apiResource('devoluciones', DevolucionController::class)->only(['store','update','destroy']);
        Route::post('bajas/{id}/archivo',  [BajaController::class, 'subirArchivo']);
        Route::apiResource('bajas', BajaController::class)->only(['store','update','destroy']);
        Route::post('movimientos/traslado',           [MovimientoProductoController::class, 'traslado']);
        Route::get('movimientos/historial',           [MovimientoProductoController::class, 'historial']);
        Route::delete('movimientos/historial/{id}',   [MovimientoProductoController::class, 'destroyHistorial']);
        Route::post('movimientos/baja',               [MovimientoProductoController::class, 'baja']);
        Route::get('movimientos/historial-baja',      [MovimientoProductoController::class, 'historialBaja']);
        Route::delete('movimientos/historial-baja/{id}', [MovimientoProductoController::class, 'destroyBaja']);
        Route::apiResource('movimientos', MovimientoProductoController::class)->only(['store','update','destroy']);
    });

    // Todos los autenticados — solo lectura y funciones generales
    Route::apiResource('estados', EstadoController::class)->only(['index','show']);
    Route::apiResource('sucursales', SucursalController::class)->only(['index','show']);
    Route::apiResource('tipos-producto', TipoProductoController::class)->only(['index','show']);
    Route::apiResource('marcas', MarcaProductoController::class)->only(['index','show']);
    Route::apiResource('productos', ProductoController::class)->only(['index','show']);
    Route::get('movimientos/producto/{id}', [MovimientoProductoController::class, 'porProducto']);
    Route::get('productos/{id}/trazabilidad',       [ProductoController::class, 'trazabilidad']);
    Route::get('trazabilidad/serie/{nserie}',       [ProductoController::class, 'trazabilidadSerie']);
    Route::apiResource('movimientos', MovimientoProductoController::class)->only(['index','show']);
    Route::apiResource('ingresos', IngresoController::class)->only(['index','show']);
    Route::get('egresos/stock',            [EgresoController::class, 'stock']);
    Route::get('egresos/{id}/archivo',     [EgresoController::class, 'descargarArchivo']);
    Route::apiResource('egresos', EgresoController::class)->only(['index','show']);
    Route::get('prestamos/{id}/archivo',   [PrestamoController::class, 'descargarArchivo']);
    Route::apiResource('prestamos', PrestamoController::class)->only(['index','show']);
    Route::apiResource('devoluciones', DevolucionController::class)->only(['index','show']);
    Route::get('bajas/{id}/archivo',   [BajaController::class, 'descargarArchivo']);
    Route::apiResource('bajas', BajaController::class)->only(['index','show']);
    Route::get('mikrotik/status',          [MikroTikController::class, 'status']);
    Route::get('mikrotik/red-sucursal',    [MikroTikController::class, 'redSucursal']);
    Route::get('mikrotik/enlaces',         [MikroTikController::class, 'enlaces']);
    Route::get('bitacora',                 [BitacoraController::class, 'index']);
    Route::post('bitacora',                [BitacoraController::class, 'store']);
    Route::delete('bitacora/{id}',         [BitacoraController::class, 'destroy']);
});
