<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FondoDetail extends Model {

    protected $table = 'fondo_details';
    protected $fillable = [
        'code_header_id',
        'type_transaction_id',
        'type_transaction_name',
        'type_transaction_action',
        'valor',
        'saldo_general',
        'fondo_details',
        'user_created_id',
        'user_created_name',
        'date_created',
        'hour_created',
        'observation_created',
        'user_cancel_id',
        'user_cancel_name',
        'observation_cancel',
    ];

}
