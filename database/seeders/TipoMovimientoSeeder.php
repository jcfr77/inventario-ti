<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMovimientoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_tipo_movimiento')->insert([
            ['ID_TIPO_MOV' => 1, 'DESCRI_TIPO_MOV' => 'INGRESO'],
            ['ID_TIPO_MOV' => 2, 'DESCRI_TIPO_MOV' => 'EGRESO'],
            ['ID_TIPO_MOV' => 3, 'DESCRI_TIPO_MOV' => 'PRESTAMO'],
            ['ID_TIPO_MOV' => 4, 'DESCRI_TIPO_MOV' => 'DEVOLUCION'],
            ['ID_TIPO_MOV' => 5, 'DESCRI_TIPO_MOV' => 'BAJA'],
        ]);
    }
}
