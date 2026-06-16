<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_estado', function (Blueprint $table) {
            $table->increments('ID_ESTADO');
            $table->text('NOMBRE_ESTADO')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_estado');
    }
};
