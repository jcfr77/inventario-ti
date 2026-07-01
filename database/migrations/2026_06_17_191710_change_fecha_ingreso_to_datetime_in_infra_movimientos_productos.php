<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infra_movimientos_productos', function (Blueprint $table) {
            $table->dateTime('FECHA_INGRESO')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('infra_movimientos_productos', function (Blueprint $table) {
            $table->date('FECHA_INGRESO')->nullable()->change();
        });
    }
};
