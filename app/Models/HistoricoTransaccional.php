<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoTransaccional extends Model
{
    protected $table = 'historico_transaccionals';
    protected $fillable = [
        'id',
        'transacciones_cuentas_id',
        'trasaccion_cuentas_ejecucions_id',
        'customer_tipo_ahorros_id',
        'customer_id',
        'type_transaction_id',
        'valor',
        'date_created',
        'hour_created',
    ];
}
