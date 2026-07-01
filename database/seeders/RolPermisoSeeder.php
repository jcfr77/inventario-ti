<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        // Todos los permisos disponibles (la visibilidad del menú se rige por grupos por usuario,
        // no por esta tabla, pero se mantiene para compatibilidad con el sistema de permisos heredado)
        $todosLosPermisos = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,23,24,25,26];

        foreach ([1, 2, 3, 4] as $idRol) {
            foreach ($todosLosPermisos as $idPermiso) {
                DB::table('infra_rol_permiso')->insertOrIgnore(['ID_ROL' => $idRol, 'ID_PERMISO' => $idPermiso]);
            }
        }
    }
}
