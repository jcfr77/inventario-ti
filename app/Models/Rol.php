<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table      = 'infra_rol';
    protected $primaryKey = 'ID_ROL';
    public $timestamps    = false;

    protected $fillable = ['NOMBRE_ROL', 'DESCRIPCION'];

    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,
            'infra_rol_permiso',
            'ID_ROL',
            'ID_PERMISO'
        );
    }
}
