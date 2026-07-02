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

        $idUsuario = DB::table('infra_usuario')->where('EMAIL', 'admin@cormudesi.cl')->value('ID_USUARIO');

        $grupos = ['mantenedores', 'activo-en-uso', 'inventario', 'informes', 'monitoreo', 'sistema'];

        foreach ($grupos as $grupo) {
            DB::table('infra_usuario_grupo')->updateOrInsert(
                ['ID_USUARIO' => $idUsuario, 'GRUPO' => $grupo],
                ['TIPO_ACCESO' => 'CRUD']
            );
        }
    }
}
