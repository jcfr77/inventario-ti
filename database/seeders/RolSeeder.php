<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_rol')->insert([
            ['ID_ROL' => 1, 'NOMBRE_ROL' => 'Administrador',  'DESCRIPCION' => 'Acceso total al sistema'],
            ['ID_ROL' => 2, 'NOMBRE_ROL' => 'Técnico TI',     'DESCRIPCION' => 'Gestión de productos y movimientos'],
            ['ID_ROL' => 3, 'NOMBRE_ROL' => 'Supervisor',     'DESCRIPCION' => 'Solo lectura e informes'],
        ]);
    }
}
