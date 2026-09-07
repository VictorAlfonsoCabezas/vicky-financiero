<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulos extends Model
{
    protected $table = 'modulos';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'description',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }  
}
