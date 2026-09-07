<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilidad extends Model
{
    protected $table = 'utilidads';
    protected $fillable = [
        'company_id',
        'numero_socios',
        'porcentaje',
        'pago_automatico',
        'fecha_pago_automatico',
        'tipo_ahorros_id',
        'cuenta_pasivo_id',
    ];

    protected $casts = [
        'pago_automatico' => 'boolean',
        'porcentaje' => 'decimal:2',
        'fecha_pago_automatico' => 'date'
    ];
}
