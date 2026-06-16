<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_tipo_producto')->insert([
            ['ID_TIPO_PRO' =>  1, 'NOMBRE_TIPO' => 'COMPUTADOR ALL-IN-ONE'],
            ['ID_TIPO_PRO' =>  2, 'NOMBRE_TIPO' => 'NOTEBOOK'],
            ['ID_TIPO_PRO' =>  3, 'NOMBRE_TIPO' => 'IMPRESORA DE TINTA'],
            ['ID_TIPO_PRO' =>  4, 'NOMBRE_TIPO' => 'IMPRESORA LASER'],
            ['ID_TIPO_PRO' =>  5, 'NOMBRE_TIPO' => 'IMPRESORA TERMICA'],
            ['ID_TIPO_PRO' =>  6, 'NOMBRE_TIPO' => 'RELOJ CONTROL'],
            ['ID_TIPO_PRO' =>  7, 'NOMBRE_TIPO' => 'COMPUTADOR TORRE'],
            ['ID_TIPO_PRO' =>  8, 'NOMBRE_TIPO' => 'ANTENAS UNIFI'],
            ['ID_TIPO_PRO' =>  9, 'NOMBRE_TIPO' => 'ANTENAS STARLINK'],
            ['ID_TIPO_PRO' => 10, 'NOMBRE_TIPO' => 'SCANNER'],
            ['ID_TIPO_PRO' => 11, 'NOMBRE_TIPO' => 'MONITOR'],
            ['ID_TIPO_PRO' => 17, 'NOMBRE_TIPO' => 'TELEVISOR'],
            ['ID_TIPO_PRO' => 18, 'NOMBRE_TIPO' => 'PROYECTOR'],
            ['ID_TIPO_PRO' => 23, 'NOMBRE_TIPO' => 'SWITCH'],
            ['ID_TIPO_PRO' => 24, 'NOMBRE_TIPO' => 'ROUTER'],
            ['ID_TIPO_PRO' => 26, 'NOMBRE_TIPO' => 'DVR'],
            ['ID_TIPO_PRO' => 27, 'NOMBRE_TIPO' => 'ANTENAS UBIQUITI'],
            ['ID_TIPO_PRO' => 29, 'NOMBRE_TIPO' => 'TECLADO'],
            ['ID_TIPO_PRO' => 31, 'NOMBRE_TIPO' => 'HUELLERO'],
            ['ID_TIPO_PRO' => 32, 'NOMBRE_TIPO' => 'BOTELLA TINTA'],
            ['ID_TIPO_PRO' => 33, 'NOMBRE_TIPO' => 'KIT TECLADO/MOUSE'],
            ['ID_TIPO_PRO' => 34, 'NOMBRE_TIPO' => 'POE'],
            ['ID_TIPO_PRO' => 35, 'NOMBRE_TIPO' => 'CABLE DE ENERGIA TREBOL'],
            ['ID_TIPO_PRO' => 36, 'NOMBRE_TIPO' => 'ESCANER'],
            ['ID_TIPO_PRO' => 37, 'NOMBRE_TIPO' => 'DISCO DURO'],
            ['ID_TIPO_PRO' => 38, 'NOMBRE_TIPO' => 'TELEFONO CELULAR'],
            ['ID_TIPO_PRO' => 39, 'NOMBRE_TIPO' => 'ALARGADOR'],
        ]);
    }
}
