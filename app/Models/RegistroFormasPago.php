<?php

namespace App\Models;

use App\Models\Bancos;
use Illuminate\Database\Eloquent\Model;

class RegistroFormasPago extends Model
{
    protected $table = "registro_formas_pagos";
    protected $fillable = [
        'company_id',
        'customer_movimientos_id',
        'customer_id',
        'letra_id',
        'deposito_id',
        'liquidacion',
        'prestamo_id',
        'forma_pago',
        'forma_pago_id',
        'banco_id',
        'valor',
        'numero_comprobante',

        'fecha_comprobante',
        'hora_comprobante',

        'solicitado',
        'usuario_solicitud',
        'usuario_id_solicitud',
        'fecha_solicitud',
        'hora_solicitud',
        'date_create',
        'hour_create',
        'user_id',
        'user_name',
        'status'
    ];

    public function banco()
    {
        return $this->belongsTo(Bancos::class, 'banco_id');
    }
}
