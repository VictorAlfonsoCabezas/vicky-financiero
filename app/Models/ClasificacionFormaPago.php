<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClasificacionFormaPago extends Model
{
    protected $table = 'clasificacion_forma_pagos';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'descripcion',
        'code',
        'status'
    ];
}
