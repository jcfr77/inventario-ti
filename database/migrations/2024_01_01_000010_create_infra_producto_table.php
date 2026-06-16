<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_producto', function (Blueprint $table) {
            $table->increments('ID_PRODUCTO');
            $table->unsignedInteger('ID_TIPO_PRO')->nullable();
            $table->text('MODELO_PRO')->nullable();
            $table->text('PROCESADOR')->nullable();
            $table->text('RAM')->nullable();
            $table->unsignedInteger('ID_MARCA')->nullable();

            $table->foreign('ID_TIPO_PRO')->references('ID_TIPO_PRO')->on('infra_tipo_producto')->nullOnDelete();
            $table->foreign('ID_MARCA')->references('ID_MARCA')->on('infra_marca_producto')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_producto');
    }
};
