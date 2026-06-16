<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_permiso', function (Blueprint $table) {
            $table->increments('ID_PERMISO');
            $table->string('CLAVE', 100)->unique();
            $table->string('ETIQUETA', 100);
            $table->string('ICONO', 10)->nullable();
            $table->string('GRUPO', 50)->nullable();
            $table->integer('ORDEN')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_permiso');
    }
};
