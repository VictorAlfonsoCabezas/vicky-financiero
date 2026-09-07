<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parentezco extends Model
{
    protected $table = 'parentezco';
    protected $fillable = [
        'id',
        'nombre',
        'defecto',
        'description',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
