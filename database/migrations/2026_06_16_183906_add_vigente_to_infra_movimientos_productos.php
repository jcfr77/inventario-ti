<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infra_movimientos_productos', function (Blueprint $table) {
            $table->tinyInteger('VIGENTE')->default(1)->after('FECHA_INGRESO');
        });
    }

    public function down(): void
    {
        Schema::table('infra_movimientos_productos', function (Blueprint $table) {
            $table->dropColumn('VIGENTE');
        });
    }
};
