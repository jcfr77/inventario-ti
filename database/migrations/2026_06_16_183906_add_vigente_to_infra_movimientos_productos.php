<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('infra_movimientos_productos', function (Blueprint $table) {
            $table->tinyInteger('VIGENTE')->default(1)->after('FECHA_INGRESO');
        });

        DB::table('infra_movimientos_productos')->update(['VIGENTE' => 1]);
    }

    public function down(): void
    {
        Schema::table('infra_movimientos_productos', function (Blueprint $table) {
            $table->dropColumn('VIGENTE');
        });
    }
};
