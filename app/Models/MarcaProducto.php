<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcaProducto extends Model
{
    protected $table = 'infra_marca_producto';
    protected $primaryKey = 'ID_MARCA';
    public $timestamps = false;

    protected $fillable = ['DESCRIPCION_MARCA'];
}
