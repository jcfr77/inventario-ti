<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'infra_sucursal';
    protected $primaryKey = 'ID_SUCURSAL';
    public $timestamps = false;

    protected $fillable = ['NOMBRE_SUCURSAL'];

    public function movimientos()
    {
        return $this->hasMany(MovimientoProducto::class, 'ID_SUCURSAL', 'ID_SUCURSAL');
    }
}
