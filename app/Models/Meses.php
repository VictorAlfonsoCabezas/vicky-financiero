<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meses extends Model
{
    protected $table = 'meses';
    protected $fillable = [
        'id',
        'company_id',
        'codigo',
        'mes',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

}
