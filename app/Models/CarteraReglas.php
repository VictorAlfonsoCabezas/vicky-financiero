<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteraReglas extends Model
{
    protected $table = 'cartera_reglas';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'mensaje',
        'comparacion',
        'dias_mora',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
