<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_tipo_movimiento', function (Blueprint $table) {
            $table->increments('ID_TIPO_MOV');
            $table->string('DESCRI_TIPO_MOV', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_tipo_movimiento');
    }
};
