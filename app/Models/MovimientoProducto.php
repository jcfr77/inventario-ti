<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoProducto extends Model
{
    protected $table = 'infra_movimientos_productos';
    protected $primaryKey = 'ID_MOVIMIENTO';
    public $timestamps = false;

    protected $fillable = [
        'ID_SUCURSAL',
        'ID_ESTADO',
        'ID_PRODUCTO',
        'NSERIE_PRO',
        'MAC_PRODUCTO',
        'IP_INTERNA',
        'UBICACION_PRO',
        'OBS_BAJA',
        'FECHA_INGRESO',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'ID_SUCURSAL', 'ID_SUCURSAL');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'ID_ESTADO', 'ID_ESTADO');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_PRODUCTO', 'ID_PRODUCTO');
    }
}
