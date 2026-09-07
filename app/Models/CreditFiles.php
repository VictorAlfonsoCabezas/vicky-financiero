<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditFiles extends Model {

    protected $table = 'credit_file';
    protected $fillable = [
        'id',
        'company_id',
        'credit_header_id',
        'date_created',
        'hour_created',
        'descripcion',
        'archivo',
        'path',
        'formato'
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
