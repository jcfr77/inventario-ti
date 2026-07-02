<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_usuario_sucursal', function (Blueprint $table) {
            $table->unsignedInteger('ID_USUARIO');
            $table->unsignedInteger('ID_SUCURSAL');
            $table->primary(['ID_USUARIO', 'ID_SUCURSAL']);
            $table->foreign('ID_USUARIO')->references('ID_USUARIO')->on('infra_usuario')->onDelete('cascade');
            $table->foreign('ID_SUCURSAL')->references('ID_SUCURSAL')->on('infra_sucursal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_usuario_sucursal');
    }
};
