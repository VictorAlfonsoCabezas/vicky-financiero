<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerts extends Model {

    protected $table = 'alerts';
    protected $fillable = [
        'company_id',
        'type',
        'name',
        'description',
        'estatus_view',
        'credit_detail_id',
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
