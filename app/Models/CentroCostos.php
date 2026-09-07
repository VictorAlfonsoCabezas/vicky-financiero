<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroCostos extends Model
{
    protected $table = 'centro_costos';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'descripcion',
        'codigo',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
