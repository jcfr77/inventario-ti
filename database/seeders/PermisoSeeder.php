<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_permiso')->insertOrIgnore([
            // General (siempre visibles para todos los usuarios)
            ['ID_PERMISO' =>  1, 'CLAVE' => 'dashboard',                        'ETIQUETA' => 'Inicio / Dashboard',  'ICONO' => '📊', 'GRUPO' => 'General',             'ORDEN' =>  1],
            ['ID_PERMISO' =>  2, 'CLAVE' => 'bitacora',                          'ETIQUETA' => 'Bitácora',            'ICONO' => '📋', 'GRUPO' => 'Sistema',             'ORDEN' =>  2],
            ['ID_PERMISO' =>  3, 'CLAVE' => 'manual',                            'ETIQUETA' => 'Ayuda',               'ICONO' => '❓', 'GRUPO' => 'General',             'ORDEN' =>  3],
            // Tablas Maestras
            ['ID_PERMISO' =>  4, 'CLAVE' => 'sucursales',                        'ETIQUETA' => 'Sucursales',          'ICONO' => '🏢', 'GRUPO' => 'Tablas Maestras',     'ORDEN' => 10],
            ['ID_PERMISO' =>  5, 'CLAVE' => 'tipos-producto',                    'ETIQUETA' => 'Tipos de Producto',   'ICONO' => '📦', 'GRUPO' => 'Tablas Maestras',     'ORDEN' => 11],
            ['ID_PERMISO' =>  6, 'CLAVE' => 'marcas',                            'ETIQUETA' => 'Marcas',              'ICONO' => '🏷️', 'GRUPO' => 'Tablas Maestras',     'ORDEN' => 12],
            ['ID_PERMISO' =>  7, 'CLAVE' => 'productos',                         'ETIQUETA' => 'Productos',           'ICONO' => '🖥️', 'GRUPO' => 'Tablas Maestras',     'ORDEN' => 13],
            ['ID_PERMISO' =>  8, 'CLAVE' => 'roles',                             'ETIQUETA' => 'Roles',               'ICONO' => '🔐', 'GRUPO' => 'Tablas Maestras',     'ORDEN' => 14],
            ['ID_PERMISO' =>  9, 'CLAVE' => 'usuarios',                          'ETIQUETA' => 'Usuarios',            'ICONO' => '👤', 'GRUPO' => 'Tablas Maestras',     'ORDEN' => 15],
            // Activo en Uso
            ['ID_PERMISO' => 10, 'CLAVE' => 'movimientos',                       'ETIQUETA' => 'Registrar',           'ICONO' => '🔄', 'GRUPO' => 'Mov. Activo en Uso',  'ORDEN' => 20],
            ['ID_PERMISO' => 23, 'CLAVE' => 'movimientos/activo-en-uso/traslado','ETIQUETA' => 'Traslado',            'ICONO' => '🚚', 'GRUPO' => 'Mov. Activo en Uso',  'ORDEN' => 21],
            ['ID_PERMISO' => 26, 'CLAVE' => 'movimientos/activo-en-uso/baja',    'ETIQUETA' => 'Baja Activo',         'ICONO' => '🗑️', 'GRUPO' => 'Mov. Activo en Uso',  'ORDEN' => 22],
            // Inventario
            ['ID_PERMISO' => 11, 'CLAVE' => 'movimientos/ingreso',               'ETIQUETA' => 'Ingreso',             'ICONO' => '📥', 'GRUPO' => 'Mov. Inventario',     'ORDEN' => 30],
            ['ID_PERMISO' => 12, 'CLAVE' => 'movimientos/egreso',                'ETIQUETA' => 'Egreso',              'ICONO' => '📤', 'GRUPO' => 'Mov. Inventario',     'ORDEN' => 31],
            ['ID_PERMISO' => 13, 'CLAVE' => 'movimientos/prestamo',              'ETIQUETA' => 'Préstamo',            'ICONO' => '🔁', 'GRUPO' => 'Mov. Inventario',     'ORDEN' => 32],
            ['ID_PERMISO' => 14, 'CLAVE' => 'movimientos/devolucion',            'ETIQUETA' => 'Devolución',          'ICONO' => '↩️', 'GRUPO' => 'Mov. Inventario',     'ORDEN' => 33],
            ['ID_PERMISO' => 15, 'CLAVE' => 'movimientos/baja',                  'ETIQUETA' => 'Baja',                'ICONO' => '🚫', 'GRUPO' => 'Mov. Inventario',     'ORDEN' => 34],
            ['ID_PERMISO' => 24, 'CLAVE' => 'movimientos/inventario',            'ETIQUETA' => 'Inventario',          'ICONO' => '📋', 'GRUPO' => 'Mov. Inventario',     'ORDEN' => 35],
            // Informes
            ['ID_PERMISO' => 16, 'CLAVE' => 'informes/sede',                     'ETIQUETA' => 'Informe Por Sede',    'ICONO' => '🏢', 'GRUPO' => 'Informes',            'ORDEN' => 40],
            ['ID_PERMISO' => 17, 'CLAVE' => 'informes/producto',                 'ETIQUETA' => 'Informe Por Producto','ICONO' => '🖥️', 'GRUPO' => 'Informes',            'ORDEN' => 41],
            ['ID_PERMISO' => 21, 'CLAVE' => 'informes/stock',                    'ETIQUETA' => 'Stock',               'ICONO' => '📦', 'GRUPO' => 'Informes',            'ORDEN' => 42],
            ['ID_PERMISO' => 25, 'CLAVE' => 'informes/trazabilidad',             'ETIQUETA' => 'Trazabilidad',        'ICONO' => '🔍', 'GRUPO' => 'Informes',            'ORDEN' => 43],
            // Monitoreo
            ['ID_PERMISO' => 18, 'CLAVE' => 'mikrotik',                          'ETIQUETA' => 'MikroTik',            'ICONO' => '📡', 'GRUPO' => 'Monitoreo',           'ORDEN' => 50],
            ['ID_PERMISO' => 19, 'CLAVE' => 'mikrotik/red-sucursal',             'ETIQUETA' => 'Red Sucursal',        'ICONO' => '🔍', 'GRUPO' => 'Monitoreo',           'ORDEN' => 51],
            ['ID_PERMISO' => 20, 'CLAVE' => 'mikrotik/enlaces',                  'ETIQUETA' => 'Enlaces de Internet', 'ICONO' => '🌐', 'GRUPO' => 'Monitoreo',           'ORDEN' => 52],
        ]);
    }
}
