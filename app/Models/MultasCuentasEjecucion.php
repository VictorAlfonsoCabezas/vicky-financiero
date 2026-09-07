<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultasCuentasEjecucion extends Model
{
    protected $table = 'multas_cuentas_ejecucions';
    protected $fillable = [
        'id',
        'transacciones_cuentas_id',
        'trasaccion_cuentas_ejecucions_id',
        'customer_tipo_ahorros_id',
        'customer_id',
        'valor_multa',
        'date_created',
        'hour_created',
        'status',
    ];
}
