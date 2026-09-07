<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impuestos extends Model
{
    protected $table = 'impuestos';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'descripcion',
        'valor',
        'codigo',
        'por_defecto',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
