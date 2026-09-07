<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormasPago extends Model
{
    protected $table = 'formas_pago';
    protected $fillable = [
        'company_id',
        'clasificacion_forma_pagos_id',
        'nombre',
        'descripcion',
        'code',
        'status',
        'credito_descargo_boveda',
    ];
}
