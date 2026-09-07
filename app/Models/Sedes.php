<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sedes extends Model
{
    protected $table = 'sedes';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'descripcion',
        'establecimiento',
        'matriz',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
