<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ReorganizarGruposMovimientosPermisos extends Migration
{
    public function up()
    {
        // Activo en Uso: Registrar (cambia grupo y etiqueta)
        DB::table('infra_permiso')->where('ID_PERMISO', 10)->update([
            'ETIQUETA' => 'Registrar',
            'GRUPO'    => 'Mov. Activo en Uso',
            'ORDEN'    => 20,
        ]);

        // Activo en Uso: Traslado / Baja (permiso nuevo)
        if (!DB::table('infra_permiso')->where('CLAVE', 'movimientos/activo-en-uso')->exists()) {
            DB::table('infra_permiso')->insert([
                'CLAVE'    => 'movimientos/activo-en-uso',
                'ETIQUETA' => 'Traslado / Baja Activo',
                'ICONO'    => '🔀',
                'GRUPO'    => 'Mov. Activo en Uso',
                'ORDEN'    => 21,
            ]);
        }

        // Inventario: cambiar grupo de Ingreso, Egreso, Préstamo, Devolución, Baja
        DB::table('infra_permiso')->whereIn('ID_PERMISO', [11, 12, 13, 14, 15])->update([
            'GRUPO' => 'Mov. Inventario',
        ]);

        // Trazabilidad: agregar si no existe
        if (!DB::table('infra_permiso')->where('CLAVE', 'informes/trazabilidad')->exists()) {
            DB::table('infra_permiso')->insert([
                'CLAVE'    => 'informes/trazabilidad',
                'ETIQUETA' => 'Trazabilidad',
                'ICONO'    => '🔍',
                'GRUPO'    => 'Informes',
                'ORDEN'    => 34,
            ]);
        }
    }

    public function down()
    {
        DB::table('infra_permiso')->where('ID_PERMISO', 10)->update([
            'ETIQUETA' => 'Movimientos (Reg.)',
            'GRUPO'    => 'Movimientos',
            'ORDEN'    => 20,
        ]);
        DB::table('infra_permiso')->where('CLAVE', 'movimientos/activo-en-uso')->delete();
        DB::table('infra_permiso')->whereIn('ID_PERMISO', [11, 12, 13, 14, 15])->update(['GRUPO' => 'Movimientos']);
        DB::table('infra_permiso')->where('CLAVE', 'informes/trazabilidad')->delete();
    }
}
