<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartolaDetail extends Model {

    protected $table = 'cartola_details';
    protected $fillable = [
        'cartola_header_code',
        'customer_movimientos_id',
        'type_transaction_id',
        'type_transaction_name',
        'type_transaction_action',
        'valor_transaction',
        'date_transaction',
        'saldo_transaction',
        'interes_id',
        'interes_valor',
        'cartola_headers_id',
        'cara',
        'posicion',
    ];

}
