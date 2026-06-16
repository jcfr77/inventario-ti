<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_rol_permiso', function (Blueprint $table) {
            $table->unsignedInteger('ID_ROL');
            $table->unsignedInteger('ID_PERMISO');
            $table->primary(['ID_ROL', 'ID_PERMISO']);

            $table->foreign('ID_ROL')->references('ID_ROL')->on('infra_rol')->onDelete('cascade');
            $table->foreign('ID_PERMISO')->references('ID_PERMISO')->on('infra_permiso')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_rol_permiso');
    }
};
