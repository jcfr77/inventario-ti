<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_usuario_permiso', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('ID_USUARIO');
            $table->integer('ID_PERMISO');
            $table->unique(['ID_USUARIO', 'ID_PERMISO']);

            $table->foreign('ID_USUARIO')->references('ID_USUARIO')->on('infra_usuario')->onDelete('cascade');
            $table->foreign('ID_PERMISO')->references('ID_PERMISO')->on('infra_permiso')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_usuario_permiso');
    }
};
