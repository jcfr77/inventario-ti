<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table      = 'infra_usuario';
    protected $primaryKey = 'ID_USUARIO';
    public $timestamps    = true;

    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = ['NOMBRE', 'EMAIL', 'PASSWORD', 'ID_ROL', 'ACTIVO', 'DEBE_CAMBIAR_PASSWORD'];

    protected $casts = ['DEBE_CAMBIAR_PASSWORD' => 'boolean'];

    protected $hidden = ['PASSWORD', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'ID_ROL', 'ID_ROL');
    }

    public function logs()
    {
        return $this->hasMany(Bitacora::class, 'ID_USUARIO', 'ID_USUARIO');
    }

    public function sucursales()
    {
        return $this->belongsToMany(
            \App\Models\Sucursal::class,
            'infra_usuario_sucursal',
            'ID_USUARIO',
            'ID_SUCURSAL'
        );
    }

    /** true si el usuario puede ver todas las sedes (admin o sin restricción asignada) */
    public function verTodasSedes(): bool
    {
        if ($this->ID_ROL === 1) return true;
        return $this->sucursales()->doesntExist();
    }
}
