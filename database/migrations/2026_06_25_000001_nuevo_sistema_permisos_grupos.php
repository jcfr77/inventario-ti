<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar TIPO_ACCESO a infra_rol
        Schema::table('infra_rol', function (Blueprint $table) {
            $table->string('TIPO_ACCESO', 4)->default('R')->after('DESCRIPCION');
        });

        // 2. Actualizar los 3 roles existentes
        \DB::table('infra_rol')->where('ID_ROL', 1)->update([
            'NOMBRE_ROL'   => 'Super Administrador',
            'DESCRIPCION'  => 'Acceso completo: crear, ver, editar y eliminar',
            'TIPO_ACCESO'  => 'CRUD',
        ]);
        \DB::table('infra_rol')->where('ID_ROL', 2)->update([
            'NOMBRE_ROL'   => 'Administrador',
            'DESCRIPCION'  => 'Crear, ver y editar',
            'TIPO_ACCESO'  => 'CRU',
        ]);
        \DB::table('infra_rol')->where('ID_ROL', 3)->update([
            'NOMBRE_ROL'   => 'Usuario',
            'DESCRIPCION'  => 'Crear y ver',
            'TIPO_ACCESO'  => 'CR',
        ]);

        // 3. Insertar rol Lector (solo lectura)
        \DB::table('infra_rol')->insertOrIgnore([
            ['ID_ROL' => 4, 'NOMBRE_ROL' => 'Lector', 'DESCRIPCION' => 'Solo lectura', 'TIPO_ACCESO' => 'R'],
        ]);

        // 4. Crear tabla infra_usuario_grupo
        Schema::create('infra_usuario_grupo', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('ID_USUARIO')->index();
            $table->string('GRUPO', 50);
            $table->string('TIPO_ACCESO', 4)->default('R');
            $table->unique(['ID_USUARIO', 'GRUPO']);
        });

        // 5. Asignar todos los grupos a usuarios existentes según su rol actual
        $grupos     = ['mantenedores', 'activo-en-uso', 'inventario', 'informes', 'monitoreo', 'sistema'];
        $rolToTipo  = [1 => 'CRUD', 2 => 'CRU', 3 => 'CR'];
        $usuarios   = \DB::table('infra_usuario')->get();

        foreach ($usuarios as $u) {
            $tipo = $rolToTipo[$u->ID_ROL] ?? 'R';
            foreach ($grupos as $grupo) {
                \DB::table('infra_usuario_grupo')->insertOrIgnore([
                    'ID_USUARIO'  => $u->ID_USUARIO,
                    'GRUPO'       => $grupo,
                    'TIPO_ACCESO' => $tipo,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('infra_usuario_grupo');

        Schema::table('infra_rol', function (Blueprint $table) {
            $table->dropColumn('TIPO_ACCESO');
        });

        \DB::table('infra_rol')->where('ID_ROL', 4)->delete();

        \DB::table('infra_rol')->where('ID_ROL', 1)->update(['NOMBRE_ROL' => 'Administrador',  'DESCRIPCION' => '']);
        \DB::table('infra_rol')->where('ID_ROL', 2)->update(['NOMBRE_ROL' => 'Técnico TI',     'DESCRIPCION' => '']);
        \DB::table('infra_rol')->where('ID_ROL', 3)->update(['NOMBRE_ROL' => 'Supervisor',     'DESCRIPCION' => '']);
    }
};
