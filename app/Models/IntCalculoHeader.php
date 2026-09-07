<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntCalculoHeader extends Model
{
    protected $table = 'int_calculo_header';
    protected $fillable = [
        'id',
        'company_id',
        'int_reglas_id',
        'fecha_creacion',
        'anio',
        'mes',
        'clientes_total',
        'clientes_excluidos',
        'clientes_calculados',
        'user_created',
        'user_calculado',
        'estado'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function intReglas()
    {
        return $this->belongsTo('App\Models\IntReglas', 'int_reglas_id');
    }
}
