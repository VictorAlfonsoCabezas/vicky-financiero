<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerHistorial extends Model
{

    protected $table = 'customer_historials';
    protected $fillable = [
        'id',
        'company_id',
        'customer_code',
        'customer_movimiento_code',
        'afecta',
        'valor_movimiento',
        'saldo_general',
        'customer_tipo_ahorro_id',
        'type_transaction_id',
        'type_transaction_name',
        'type_transaction_action',
        'date_created',
        'hour_created',
        'status',
        'carga_masiva',
    ];

    public function tipo()
    {
        return $this->belongsTo('App\Models\TypeTransaction', 'type_transaction_id');
    }
}
