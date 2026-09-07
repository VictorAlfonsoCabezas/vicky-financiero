<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogReversoMovimiento extends Model
{
    protected $table = 'log_reverso_movimientos';
    protected $fillable = [
        'id',
        'company_id',
        'date_create',
        'hour_create',
        'detalle',
        'user_id',
        'user_name',
        'date_movimiento',
        'hour_movimiento',
        'customer_id',
        'customer_name',
        'observacion',
    ];
}
