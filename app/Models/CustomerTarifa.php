<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTarifa extends Model {

    protected $table = 'customer_tarifas';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'porcentaje',
        'date_created',
        'hour_created',
        'status'
    ];

}
