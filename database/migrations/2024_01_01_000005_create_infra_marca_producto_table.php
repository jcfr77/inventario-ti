<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_marca_producto', function (Blueprint $table) {
            $table->increments('ID_MARCA');
            $table->string('DESCRIPCION_MARCA', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_marca_producto');
    }
};
