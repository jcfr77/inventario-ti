<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeFechaIngresoToDatetimeInInfraMovimientosProductos extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE infra_movimientos_productos MODIFY COLUMN FECHA_INGRESO DATETIME NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE infra_movimientos_productos MODIFY COLUMN FECHA_INGRESO DATE NULL');
    }
}
