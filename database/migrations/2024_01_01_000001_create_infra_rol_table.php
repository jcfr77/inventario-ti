<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_rol', function (Blueprint $table) {
            $table->increments('ID_ROL');
            $table->string('NOMBRE_ROL', 50);
            $table->string('DESCRIPCION', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_rol');
    }
};
