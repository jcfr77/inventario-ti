<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_sucursal')->insertOrIgnore([
            ['ID_SUCURSAL' =>  1, 'NOMBRE_SUCURSAL' => 'CESFAM VIDELA'],
            ['ID_SUCURSAL' =>  2, 'NOMBRE_SUCURSAL' => 'CESFAM AGUIRRE'],
            ['ID_SUCURSAL' =>  3, 'NOMBRE_SUCURSAL' => 'CESFAM GUZMAN'],
            ['ID_SUCURSAL' =>  4, 'NOMBRE_SUCURSAL' => 'CESFAM SUR'],
            ['ID_SUCURSAL' =>  5, 'NOMBRE_SUCURSAL' => 'POSTA SAN MARCOS'],
            ['ID_SUCURSAL' =>  6, 'NOMBRE_SUCURSAL' => 'POSTA CHANAVAYITA'],
            ['ID_SUCURSAL' =>  7, 'NOMBRE_SUCURSAL' => 'CECOSF'],
            ['ID_SUCURSAL' =>  8, 'NOMBRE_SUCURSAL' => 'BAQUEDANO'],
            ['ID_SUCURSAL' =>  9, 'NOMBRE_SUCURSAL' => 'CENTRO MEDICO'],
            ['ID_SUCURSAL' => 10, 'NOMBRE_SUCURSAL' => 'FARMACIA COMUNAL SUR'],
            ['ID_SUCURSAL' => 11, 'NOMBRE_SUCURSAL' => 'FARMACIA COMUNAL CENTRO'],
            ['ID_SUCURSAL' => 12, 'NOMBRE_SUCURSAL' => 'OPTICA COMUNAL'],
            ['ID_SUCURSAL' => 13, 'NOMBRE_SUCURSAL' => 'CASA DE LA CULTURA'],
            ['ID_SUCURSAL' => 14, 'NOMBRE_SUCURSAL' => 'CEMENTERIO 1'],
            ['ID_SUCURSAL' => 15, 'NOMBRE_SUCURSAL' => 'CEMENTERIO 3'],
            ['ID_SUCURSAL' => 16, 'NOMBRE_SUCURSAL' => 'ESTADIO EXCODE'],
            ['ID_SUCURSAL' => 17, 'NOMBRE_SUCURSAL' => 'ESTADIO CENTRO CEA'],
            ['ID_SUCURSAL' => 18, 'NOMBRE_SUCURSAL' => 'CALETA CARAMUCHO'],
            ['ID_SUCURSAL' => 19, 'NOMBRE_SUCURSAL' => 'CALETA CHANAVAYA'],
            ['ID_SUCURSAL' => 20, 'NOMBRE_SUCURSAL' => 'CALETA CHIPANA'],
            ['ID_SUCURSAL' => 21, 'NOMBRE_SUCURSAL' => 'LOS VERDES'],
            ['ID_SUCURSAL' => 22, 'NOMBRE_SUCURSAL' => 'CASA CENTRAL'],
            ['ID_SUCURSAL' => 25, 'NOMBRE_SUCURSAL' => 'MUSEO'],
            ['ID_SUCURSAL' => 26, 'NOMBRE_SUCURSAL' => 'CANAL RTC'],
            ['ID_SUCURSAL' => 27, 'NOMBRE_SUCURSAL' => 'BODEGA ESTADIO EXCODE'],
            ['ID_SUCURSAL' => 28, 'NOMBRE_SUCURSAL' => 'BODEGA BAQUEDANO'],
        ]);
    }
}
