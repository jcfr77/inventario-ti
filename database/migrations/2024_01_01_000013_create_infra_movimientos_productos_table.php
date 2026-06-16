<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_movimientos_productos', function (Blueprint $table) {
            $table->increments('ID_MOVIMIENTO');
            $table->unsignedInteger('ID_SUCURSAL')->nullable();
            $table->unsignedInteger('ID_ESTADO')->nullable();
            $table->unsignedInteger('ID_PRODUCTO')->nullable();
            $table->text('MAC_PRODUCTO')->nullable();
            $table->text('IP_INTERNA')->nullable();
            $table->text('UBICACION_PRO')->nullable();
            $table->text('OBS_BAJA')->nullable();
            $table->text('NSERIE_PRO')->nullable();
            $table->date('FECHA_INGRESO')->nullable();

            $table->foreign('ID_SUCURSAL')->references('ID_SUCURSAL')->on('infra_sucursal')->nullOnDelete();
            $table->foreign('ID_ESTADO')->references('ID_ESTADO')->on('infra_estado')->nullOnDelete();
            $table->foreign('ID_PRODUCTO')->references('ID_PRODUCTO')->on('infra_producto')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_movimientos_productos');
    }
};
