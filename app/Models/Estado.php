<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'infra_estado';
    protected $primaryKey = 'ID_ESTADO';
    public $timestamps = false;

    protected $fillable = ['NOMBRE_ESTADO'];

    public function movimientos()
    {
        return $this->hasMany(MovimientoProducto::class, 'ID_ESTADO', 'ID_ESTADO');
    }
}
