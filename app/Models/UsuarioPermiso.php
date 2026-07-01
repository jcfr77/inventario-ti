<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPermiso extends Model
{
    protected $table      = 'infra_usuario_permiso';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = ['ID_USUARIO', 'ID_PERMISO'];
}
