<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionesHeader extends Model
{
    protected $table = 'acciones_header';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'descripcion',
        'capital',
        'anio',
        'mes',
        'class',
        'estado',
        'user_created',
        'certificado',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
