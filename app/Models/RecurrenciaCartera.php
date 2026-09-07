<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurrenciaCartera extends Model {

    protected $table = 'recurrencia_cartera';
    protected $fillable = [
        'id',
        'company_id',
        'desde',
        'hasta',
        'dias',
        'orden',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
