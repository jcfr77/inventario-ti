<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'infra_tipo_producto';
    protected $primaryKey = 'ID_TIPO_PRO';
    public $timestamps = false;

    protected $fillable = ['NOMBRE_TIPO'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'ID_TIPO_PRO', 'ID_TIPO_PRO');
    }
}
