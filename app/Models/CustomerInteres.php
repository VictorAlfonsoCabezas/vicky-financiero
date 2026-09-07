<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInteres extends Model {

    protected $table = 'customer_interes';
    protected $fillable = [
        'company_id',
        'valor',
        'customer_id',
        'customer_code',
        'customer_name',
        'mes',
        'anio',
        'calculado',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
