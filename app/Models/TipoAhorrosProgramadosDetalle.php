<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAhorrosProgramadosDetalle extends Model
{
    protected $table = 'tipo_ahorros_programados_detalle';
    protected $fillable = [
        'id',
        'company_id',
        'tipo_ahorros_id',
        'interes',
        'rango_min',
        'rango_max',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function tipoAhorros()
    {
        return $this->belongsTo('App\Models\TipoAhorros', 'tipo_ahorros_id');
    }
}
