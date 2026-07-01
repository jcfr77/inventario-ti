<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioGrupo extends Model
{
    protected $table      = 'infra_usuario_grupo';
    protected $primaryKey = 'ID';
    public    $timestamps = false;

    protected $fillable = ['ID_USUARIO', 'GRUPO', 'TIPO_ACCESO'];
}
