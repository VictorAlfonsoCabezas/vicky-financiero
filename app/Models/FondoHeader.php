<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FondoHeader extends Model {

    protected $table = 'fondo_headers';
    protected $fillable = [
        'code',
        'company_id',
        'valor_inicial',
        'valor_ingreso',
        'valor_egreso',
        'valor_total',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
        'status',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
