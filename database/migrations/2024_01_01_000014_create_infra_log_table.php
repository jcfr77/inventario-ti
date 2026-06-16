<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_log', function (Blueprint $table) {
            $table->increments('ID_LOG');
            $table->unsignedInteger('ID_USUARIO')->nullable();
            $table->string('TABLA', 50);
            $table->string('ACCION', 20);
            $table->text('DESCRIPCION')->nullable();
            $table->string('IP_CLIENTE', 45)->nullable();
            $table->timestamp('FECHA')->useCurrent();

            $table->foreign('ID_USUARIO')->references('ID_USUARIO')->on('infra_usuario')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_log');
    }
};
