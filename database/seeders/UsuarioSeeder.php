<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_usuario')->insert([
            [
                'NOMBRE'                 => 'Administrador',
                'EMAIL'                  => 'admin@cormudesi.cl',
                'PASSWORD'               => Hash::make('Admin@2024'),
                'ID_ROL'                 => 1,
                'ACTIVO'                 => 1,
                'DEBE_CAMBIAR_PASSWORD'  => 1,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
        ]);
    }
}
