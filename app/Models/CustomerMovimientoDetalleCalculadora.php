<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMovimientoDetalleCalculadora extends Model
{
    protected $table = 'customer_movimiento_detalle_calculadoras';
    protected $fillable = [
        'id',
        'customer_movimientos_id',
        'header_calculadora_id',
        'rentabilidad',
        'penalizado',
        'total',
        'fecha_pago',
        'status',
    ];
}
