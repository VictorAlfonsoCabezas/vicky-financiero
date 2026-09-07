<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garantes extends Model
{
    protected $table = 'garantes';
    protected $fillable = [
        'id',
        'company_id',
        'credit_folder_headers_id',
        'customer_id',
        'customer_name',
        'customer_identificacion',
        'customer_conyuge_name',
        'customer_conyuge_identificacion',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
