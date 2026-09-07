<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderCalculadora extends Model
{
    protected $table = 'header_calculadoras';
    protected $fillable = [
            'id',
            'company_id',
            'customer_id',
            'beneficiarioProgramado',
            'customer_movimientos_id',
            'tipo_pago',
            'tipo_ahorros_programados_detalle_id',
            'interes',
            'dias_plazo',
            'rentabilidad',
            'penalizado',
            'total',
            'date_created',
            'hour_created',
            'user_created_id',
            'user_created_name',
            'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
