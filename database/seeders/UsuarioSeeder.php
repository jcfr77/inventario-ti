<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_usuario')->updateOrInsert(
            ['EMAIL' => 'admin@cormudesi.cl'],
            [
                'NOMBRE'                => 'Administrador',
                'PASSWORD'              => Hash::make('Admin@2024'),
                'ID_ROL'                => 1,
                'ACTIVO'                => 1,
                'DEBE_CAMBIAR_PASSWORD' => 1,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]
        );
    }
}
