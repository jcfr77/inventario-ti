<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleMov extends Model
{
    protected $table      = 'infra_detalle_mov';
    protected $primaryKey = 'ID_DETALLE';
    public $timestamps    = false;

    protected $fillable = ['ID_PRODUCTO', 'ID_ENCABEZADO', 'NSERIEDETA', 'DETA_CANT'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_PRODUCTO', 'ID_PRODUCTO')
                    ->with(['tipo', 'marca']);
    }
}
