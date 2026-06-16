<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador — acceso total
        $adminPermisos = range(1, 21);
        foreach ($adminPermisos as $idPermiso) {
            DB::table('infra_rol_permiso')->insert(['ID_ROL' => 1, 'ID_PERMISO' => $idPermiso]);
        }

        // Técnico TI
        foreach ([1, 3, 10, 18, 19, 20, 21] as $idPermiso) {
            DB::table('infra_rol_permiso')->insert(['ID_ROL' => 2, 'ID_PERMISO' => $idPermiso]);
        }

        // Supervisor
        foreach ([1, 2, 3, 16, 17, 18, 19, 20, 21] as $idPermiso) {
            DB::table('infra_rol_permiso')->insert(['ID_ROL' => 3, 'ID_PERMISO' => $idPermiso]);
        }
    }
}
