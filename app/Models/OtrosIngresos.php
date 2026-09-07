<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtrosIngresos extends Model
{
    protected $table = 'otros_ingresos';
    protected $fillable = [
        'id',
        'company_id',
        'transaction_id',
        'movimiento_id',
        'name',
        'descripcion',
        'valor',
        'date_create',
        'hour_create',
        'user_id',
        'user_name',
    ];
}
