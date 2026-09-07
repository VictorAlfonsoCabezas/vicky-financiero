<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTipoAhorros extends Model
{
    protected $table = 'customer_tipo_ahorros';
    protected $fillable = [
        'id',
        'company_id',
        'customer_id',
        'tipo_ahorros_id',
        'codigo',
        'valor',
        'tipo_ahorros_programados_detalle_id',
        'pago',
        'dias_plazo',
        'cumplimiento',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function tipoAhorrosProgramadosDetalle()
    {
        return $this->belongsTo('App\Models\TipoAhorrosProgramadosDetalle', 'tipo_ahorros_programados_detalle_id');
    }

    public function banco()
    {
        return $this->belongsTo(\App\Models\Bancos::class, 'banco_id');
    }


    public function tipoAhorros()
    {
        return $this->belongsTo('App\Models\TipoAhorros', 'tipo_ahorros_id');
    }
}
