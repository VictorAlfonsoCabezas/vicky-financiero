<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteraHeader extends Model {

    protected $table = 'cartera_header';
    protected $fillable = [
        'id' ,
        'company_id',
        'mes',
        'mes_name',
        'year',
        'tipo',
        'date_save',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
