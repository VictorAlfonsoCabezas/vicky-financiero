<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaEncajes extends Model
{
    protected $table = 'entrega_encajes';
    protected $fillable = [
        'customer_id',
        'customer_tipo_ahorro_id',
        'valor',
        'porcentaje_retenido',
        'valor_retenido',
        'valor_entregado',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
        'user_cancel_id',
        'user_cancel_name',
        'date_cancel',
        'hour_cancel',
        'user_entrega_id',
        'user_entrega_name',
        'date_entrega',
        'hour_entrega',
        'status',

        'porcentaje_plazo_fijo',
        'valor_plazo_fijo',
        'taza_plazo_fijo',
        'pago_plazo_fijo',
        'dias_plazo_fijo',
        'beneficiario_plazo_fijo',
    ];
}
