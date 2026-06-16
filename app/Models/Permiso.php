<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table      = 'infra_permiso';
    protected $primaryKey = 'ID_PERMISO';
    public $timestamps    = false;

    protected $fillable = ['CLAVE', 'ETIQUETA', 'ICONO', 'GRUPO', 'ORDEN'];
}
