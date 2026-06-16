<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('infra_marca_producto')->insert([
            ['ID_MARCA' =>  1, 'DESCRIPCION_MARCA' => 'HP'],
            ['ID_MARCA' =>  2, 'DESCRIPCION_MARCA' => 'LENOVO'],
            ['ID_MARCA' =>  3, 'DESCRIPCION_MARCA' => 'ASUS'],
            ['ID_MARCA' =>  4, 'DESCRIPCION_MARCA' => 'EPSON'],
            ['ID_MARCA' =>  5, 'DESCRIPCION_MARCA' => 'LG'],
            ['ID_MARCA' =>  7, 'DESCRIPCION_MARCA' => 'AOC'],
            ['ID_MARCA' =>  8, 'DESCRIPCION_MARCA' => 'ROJEM'],
            ['ID_MARCA' =>  9, 'DESCRIPCION_MARCA' => 'MASTER G'],
            ['ID_MARCA' => 10, 'DESCRIPCION_MARCA' => 'XEROX'],
            ['ID_MARCA' => 11, 'DESCRIPCION_MARCA' => 'SAMSUNG'],
            ['ID_MARCA' => 12, 'DESCRIPCION_MARCA' => 'RICOH'],
            ['ID_MARCA' => 13, 'DESCRIPCION_MARCA' => 'CISCO'],
            ['ID_MARCA' => 14, 'DESCRIPCION_MARCA' => 'TP-LINK'],
            ['ID_MARCA' => 15, 'DESCRIPCION_MARCA' => 'D-LINK'],
            ['ID_MARCA' => 16, 'DESCRIPCION_MARCA' => 'BROTHER'],
            ['ID_MARCA' => 17, 'DESCRIPCION_MARCA' => 'VIEWSONIC'],
            ['ID_MARCA' => 18, 'DESCRIPCION_MARCA' => 'GABA'],
            ['ID_MARCA' => 19, 'DESCRIPCION_MARCA' => 'UBIQUITI'],
            ['ID_MARCA' => 20, 'DESCRIPCION_MARCA' => 'MIKROTIK'],
            ['ID_MARCA' => 21, 'DESCRIPCION_MARCA' => 'XIAOMI'],
            ['ID_MARCA' => 22, 'DESCRIPCION_MARCA' => 'ZEBRA'],
            ['ID_MARCA' => 23, 'DESCRIPCION_MARCA' => 'STARLINK'],
            ['ID_MARCA' => 24, 'DESCRIPCION_MARCA' => 'DEEPCOOL'],
            ['ID_MARCA' => 25, 'DESCRIPCION_MARCA' => 'DAHUA'],
            ['ID_MARCA' => 26, 'DESCRIPCION_MARCA' => 'SONY'],
            ['ID_MARCA' => 28, 'DESCRIPCION_MARCA' => 'TECNOLAB'],
            ['ID_MARCA' => 30, 'DESCRIPCION_MARCA' => 'MSI'],
            ['ID_MARCA' => 31, 'DESCRIPCION_MARCA' => 'HID GLOBAL CORP'],
            ['ID_MARCA' => 32, 'DESCRIPCION_MARCA' => 'COMP CABLE'],
            ['ID_MARCA' => 33, 'DESCRIPCION_MARCA' => 'KNUP'],
            ['ID_MARCA' => 34, 'DESCRIPCION_MARCA' => 'ADATA'],
            ['ID_MARCA' => 35, 'DESCRIPCION_MARCA' => 'KINGSTON'],
            ['ID_MARCA' => 36, 'DESCRIPCION_MARCA' => 'GENERICO'],
            ['ID_MARCA' => 37, 'DESCRIPCION_MARCA' => 'CANON'],
            ['ID_MARCA' => 38, 'DESCRIPCION_MARCA' => 'WESTERN DIGITAL'],
            ['ID_MARCA' => 39, 'DESCRIPCION_MARCA' => 'UYUS TOOLS'],
        ]);
    }
}
