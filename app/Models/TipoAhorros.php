<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAhorros extends Model
{
    protected $table = 'tipo_ahorros';
    protected $fillable = [
        'id',
        'company_id',
        'descargo_creditos',
        'valor_periodico',
        'programado',
        'cuenta_certificado',
        'cuenta_certificado_valor_max',
        'cuenta_encaje',
        'porcentaje_encaje',
        'ahorro_prestamo',
        'name',
        'description',
        'edad_min',
        'edad_max',
        'interes',
        'rango_valor',
        'rango_tiempo',
        'class',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
