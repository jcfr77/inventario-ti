<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar TIPO_ACCESO a infra_rol
        Schema::table('infra_rol', function (Blueprint $table) {
            $table->string('TIPO_ACCESO', 4)->default('R')->after('DESCRIPCION');
        });

        // Tabla de grupos de menú por usuario (controla visibilidad y nivel de acceso)
        Schema::create('infra_usuario_grupo', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('ID_USUARIO')->index();
            $table->string('GRUPO', 50);
            $table->string('TIPO_ACCESO', 4)->default('R');
            $table->unique(['ID_USUARIO', 'GRUPO']);
            $table->foreign('ID_USUARIO')->references('ID_USUARIO')->on('infra_usuario')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_usuario_grupo');

        Schema::table('infra_rol', function (Blueprint $table) {
            $table->dropColumn('TIPO_ACCESO');
        });
    }
};
