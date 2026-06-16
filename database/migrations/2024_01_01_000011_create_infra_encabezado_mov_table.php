<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_encabezado_mov', function (Blueprint $table) {
            $table->increments('ID_ENCABEZADO');
            $table->unsignedInteger('ID_SUCURSAL')->nullable();
            $table->unsignedInteger('ID_TIPO_MOV')->nullable();
            $table->string('OCOMPRA', 50)->nullable();
            $table->integer('NFACTURA')->nullable();
            $table->integer('NGUIA')->nullable();
            $table->string('OBS_ENCA', 200)->nullable();
            $table->date('FECHA_ENCA')->nullable();
            $table->date('FECHA_FACTURA')->nullable();
            $table->integer('TOTAL_FACTURA')->nullable();
            $table->string('ARCHIVO', 255)->nullable();
            $table->integer('CORRE_AENTRE')->nullable();
            $table->date('FECHA_DEVOLUCION')->nullable();
            $table->string('RESPONSABLE', 150)->nullable();
            $table->string('PROVEEDOR', 150)->nullable();
            $table->string('MOTIVO_DEV', 300)->nullable();
            $table->string('MOTIVO_BAJA', 100)->nullable();
            $table->integer('NPRESTAMO')->nullable();

            $table->foreign('ID_SUCURSAL')->references('ID_SUCURSAL')->on('infra_sucursal')->nullOnDelete();
            $table->foreign('ID_TIPO_MOV')->references('ID_TIPO_MOV')->on('infra_tipo_movimiento')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_encabezado_mov');
    }
};
