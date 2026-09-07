<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurrenciaPrestamos extends Model {

    protected $table = 'recurencia_prestamo';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'separacion',
        'code',
        'date_created',
        'hour_created',
        'user_created',
        'user_id',
        'status',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
