<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteraEnviosHeader extends Model
{
    protected $table = 'cartera_envios_header';
    protected $fillable = [
        'id',
        'company_id',
        'cartera_reglas_id',
        'fecha_creacion',
        'anio',
        'mes',
        'user_created',
        'user_calcuado',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function certeraReglas()
    {
        return $this->belongsTo('App\Models\CarteraReglas', 'cartera_reglas_id');
    }
}
