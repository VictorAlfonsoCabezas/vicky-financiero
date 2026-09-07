<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastosFormaPagos extends Model
{
    protected $table = 'gastos_forma_pagos';
    protected $fillable = [
        'id',
        'gasto_id',
        'forma_pago_id',
        'valor',
        'fecha_creacion',
    ];

    public function gasto()
    {
        return $this->belongsTo('App\Models\Gastos', 'gasto_id');
    }
    
    public function formaPago()
    {
        return $this->belongsTo('App\Models\FormasPago', 'forma_pago_id');
    }
}
