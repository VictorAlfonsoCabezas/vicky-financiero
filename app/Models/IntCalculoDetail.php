<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntCalculoDetail extends Model
{
    protected $table = 'int_calculo_detail';
    protected $fillable = [
        'id',
        'company_id',
        'int_calculo_header_id',
        'customer_id',
        'tipo_ahorro_id',
        'customer_code',
        'valor_saldo',
        'valor_interes',
        'valor_total',
        'estado',
        'fecha_calculado',
        'user_calculado'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function customerTipoAhorros()
    {
        return $this->belongsTo('App\Models\CustomerTipoAhorros', 'tipo_ahorro_id');
    }

    public function intCalculoHeader()
    {
        return $this->belongsTo('App\Models\IntCalculoHeader', 'int_calculo_header_id');
    }
}
