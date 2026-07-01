<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,23,24,25,26];

        $filas = [];
        foreach ([1, 2, 3, 4] as $idRol) {
            foreach ($permisos as $idPermiso) {
                $filas[] = ['ID_ROL' => $idRol, 'ID_PERMISO' => $idPermiso];
            }
        }

        DB::table('infra_rol_permiso')->insertOrIgnore($filas);
    }
}
