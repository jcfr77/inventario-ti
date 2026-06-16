<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_detalle_mov', function (Blueprint $table) {
            $table->increments('ID_DETALLE');
            $table->unsignedInteger('ID_PRODUCTO')->nullable();
            $table->unsignedInteger('ID_ENCABEZADO')->nullable();
            $table->string('NSERIEDETA', 50)->nullable();
            $table->integer('DETA_CANT')->nullable();

            $table->foreign('ID_PRODUCTO')->references('ID_PRODUCTO')->on('infra_producto')->nullOnDelete();
            $table->foreign('ID_ENCABEZADO')->references('ID_ENCABEZADO')->on('infra_encabezado_mov')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_detalle_mov');
    }
};
