<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntReglas extends Model
{
    protected $table = 'int_reglas';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'texto',
        'description',
        'fecha_inicio',
        'tipo_ahorro_id',
        'interes',
        'valor',
        'operacion',
        'rango_valor',
        'rango_tiempo',
        'status',
        'created_at',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
