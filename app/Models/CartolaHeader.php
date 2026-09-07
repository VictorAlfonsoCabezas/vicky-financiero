<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartolaHeader extends Model {

    protected $table = 'cartola_headers';
    protected $fillable = [
        'code',
        'company_id',
        'customer_id',
        'customer_code',
        'customer_name',
        'ultimo_interes',
        'customer_date_create',
        'status',
        'numero',
        'date_update',
        'hour_update',
        'user_update',
        'date_create',
        'hour_create',
        'user_create',
        'customer_tipo_ahorro_id',
        
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
