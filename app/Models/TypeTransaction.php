<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeTransaction extends Model {

    protected $table = 'type_transactions';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'name_corto',
        'nombre_cartola',
        'description',
        'action',
        'afecta',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
        'status',
    ];

}
