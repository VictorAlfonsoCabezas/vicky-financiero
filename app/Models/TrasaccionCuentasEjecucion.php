<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasaccionCuentasEjecucion extends Model
{
    protected $table = 'trasaccion_cuentas_ejecucions';
    protected $fillable = [
        'id',
        'transacciones_cuentas_id',
        'month',
        'year',
        'fecha_inicial',
        'fecha_final',
        'valor_debito',
        'valor_multa',
        'date_created',
        'hour_created',
        'status',
    ];
}
