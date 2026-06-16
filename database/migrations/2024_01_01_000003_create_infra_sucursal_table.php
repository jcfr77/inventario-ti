<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_sucursal', function (Blueprint $table) {
            $table->increments('ID_SUCURSAL');
            $table->text('NOMBRE_SUCURSAL')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_sucursal');
    }
};
