<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAhorrosDetalle extends Model
{
    protected $table = 'tipo_ahorros_detalle';
    protected $fillable = [
        'id',
        'company_id',
        'tipo_ahorros_id',
        'afecta',
        'nombre',
        'valor',
        'siglas',
        'bloqueado',
        'suma',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function tipoAhorros()
    {
        return $this->belongsTo('App\Models\TipoAhorros', 'tipo_ahorros_id');
    }
}
