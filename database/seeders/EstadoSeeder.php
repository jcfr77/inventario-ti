<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_estado')->insert([
            ['ID_ESTADO' => 1, 'NOMBRE_ESTADO' => 'ACTIVO'],
            ['ID_ESTADO' => 2, 'NOMBRE_ESTADO' => 'INACTIVO'],
            ['ID_ESTADO' => 3, 'NOMBRE_ESTADO' => 'BAJA'],
        ]);
    }
}
