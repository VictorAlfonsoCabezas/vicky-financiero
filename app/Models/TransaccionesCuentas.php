<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaccionesCuentas extends Model
{
    protected $table = 'transacciones_cuentas';
    protected $fillable = [
        'id',
        'concepto',
        'dia_mes',
        'porcentaje_recaudacion',
        'valor_recaudacion',
        'dias_multa',
        'porcentaje_multa',
        'valor_multa',
        'tipo_ahorro_id',
        'banco_id'
    ];
}
