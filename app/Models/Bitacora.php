<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table      = 'infra_log';
    protected $primaryKey = 'ID_LOG';
    public $timestamps    = false;

    protected $fillable = ['ID_USUARIO', 'TABLA', 'ACCION', 'DESCRIPCION', 'IP_CLIENTE'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_USUARIO', 'ID_USUARIO');
    }

    public static function registrar($tabla, $accion, $descripcion, $ip = null, $idUsuario = null)
    {
        self::create([
            'ID_USUARIO'  => $idUsuario ?? auth()->id(),
            'TABLA'       => $tabla,
            'ACCION'      => $accion,
            'DESCRIPCION' => $descripcion,
            'IP_CLIENTE'  => $ip,
        ]);
    }
}
