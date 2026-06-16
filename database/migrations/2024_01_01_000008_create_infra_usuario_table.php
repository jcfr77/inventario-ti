<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_usuario', function (Blueprint $table) {
            $table->increments('ID_USUARIO');
            $table->string('NOMBRE', 100);
            $table->string('EMAIL', 100)->unique();
            $table->string('PASSWORD', 255);
            $table->unsignedInteger('ID_ROL')->default(1);
            $table->tinyInteger('ACTIVO')->default(1);
            $table->tinyInteger('DEBE_CAMBIAR_PASSWORD')->default(0);
            $table->timestamps();

            $table->foreign('ID_ROL')->references('ID_ROL')->on('infra_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_usuario');
    }
};
