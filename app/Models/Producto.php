<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'infra_producto';
    protected $primaryKey = 'ID_PRODUCTO';
    public $timestamps = false;

    protected $fillable = [
        'ID_TIPO_PRO',
        'ID_MARCA',
        'MODELO_PRO',
        'PROCESADOR',
        'RAM',
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoProducto::class, 'ID_TIPO_PRO', 'ID_TIPO_PRO');
    }

    public function marca()
    {
        return $this->belongsTo(MarcaProducto::class, 'ID_MARCA', 'ID_MARCA');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoProducto::class, 'ID_PRODUCTO', 'ID_PRODUCTO');
    }

    public function movimientoActual()
    {
        return $this->hasOne(MovimientoProducto::class, 'ID_PRODUCTO', 'ID_PRODUCTO')->latest('ID_MOVIMIENTO');
    }
}
