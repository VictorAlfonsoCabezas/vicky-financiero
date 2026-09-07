<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteraDetail extends Model {

    protected $table = 'cartera_detail';
    protected $fillable = [
        'id',
        'cartera_header_id',
        'recurrencia_cartera_id',
        'valores',
        'date_save',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
    ];

    public function header() {
        return $this->belongsTo('App\Models\CarteraHeader', 'cartera_header_id');
    }

    public function recurrencia() {
        return $this->belongsTo('App\Models\RecurrenciaCartera', 'recurrencia_cartera_id');
    }

}
