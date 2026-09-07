<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procesos extends Model {

    protected $table = 'procesos';
    protected $fillable = [
        'id',
        'company_id',
        'description',
        'recurrencia',
        'valor',
        'date_created',
        'hour_created',
        'dia_update',
        'mes_update',
        'anio_update',
        'date_update',
        'status'
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
