<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncabezadoMov extends Model
{
    protected $table      = 'infra_encabezado_mov';
    protected $primaryKey = 'ID_ENCABEZADO';
    public $timestamps    = false;

    protected $fillable = [
        'ID_SUCURSAL', 'ID_TIPO_MOV', 'OCOMPRA', 'NFACTURA',
        'NGUIA', 'OBS_ENCA', 'FECHA_ENCA', 'FECHA_FACTURA', 'TOTAL_FACTURA', 'ARCHIVO', 'CORRE_AENTRE',
        'FECHA_DEVOLUCION', 'RESPONSABLE', 'NPRESTAMO', 'PROVEEDOR', 'MOTIVO_DEV', 'MOTIVO_BAJA',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'ID_SUCURSAL', 'ID_SUCURSAL');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleMov::class, 'ID_ENCABEZADO', 'ID_ENCABEZADO');
    }
}
