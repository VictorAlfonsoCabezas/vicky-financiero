<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminosUso extends Model
{
    protected $table = 'terminos_usos';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'description',
        'date_create',
        'hour_create',
        'user_id',
        'user_name',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
