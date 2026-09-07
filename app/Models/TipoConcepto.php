<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoConcepto extends Model
{
    protected $table = 'tipo_concepto';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }  
}
