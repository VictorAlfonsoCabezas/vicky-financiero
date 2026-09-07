<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerFolder extends Model {

    protected $table = 'customer_folders';
    protected $fillable = [
        'id',
        'code',
        'company_id',
        'customer_code',
        'customer_name',
        'customer_ruc',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
        'status'
    ];

}
