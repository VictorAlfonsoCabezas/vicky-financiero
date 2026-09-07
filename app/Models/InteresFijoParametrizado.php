<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteresFijoParametrizado extends Model
{
    protected $table = 'interes_fijo_parametrizado';
    protected $fillable = [
        'valor_inicio',
        'valor_fin',
        'primer_valor',
        'segundo_valor',
        'tercer_valor',
        'cuarto_valor',
        'porcentaje',
        'status',
    ];
}
