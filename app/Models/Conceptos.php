<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conceptos extends Model
{
    protected $table = 'conceptos';
    protected $fillable = [
        'id',
        'company_id',
        'tipo_concepto_id',
        'nombre',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function tipoConcepto()
    {
        return $this->belongsTo('App\Models\TipoConcepto', 'tipo_concepto_id');
    }
}
