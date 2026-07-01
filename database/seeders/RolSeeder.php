<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_rol')->insertOrIgnore([
            ['ID_ROL' => 1, 'NOMBRE_ROL' => 'Super Administrador', 'DESCRIPCION' => 'Acceso completo CRUD',  'TIPO_ACCESO' => 'CRUD'],
            ['ID_ROL' => 2, 'NOMBRE_ROL' => 'Administrador',       'DESCRIPCION' => 'Crear, ver y editar',   'TIPO_ACCESO' => 'CRU'],
            ['ID_ROL' => 3, 'NOMBRE_ROL' => 'Usuario',             'DESCRIPCION' => 'Crear y ver',           'TIPO_ACCESO' => 'CR'],
            ['ID_ROL' => 4, 'NOMBRE_ROL' => 'Lector',              'DESCRIPCION' => 'Solo lectura',          'TIPO_ACCESO' => 'R'],
        ]);
    }
}
