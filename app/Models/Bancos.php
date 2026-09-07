<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bancos extends Model
{
    protected $table = 'bancos';
    protected $fillable = [
        'id',
        'company_id',
        'tipo_cuenta_id',
        'fecha_creacion',
        'nombre',
        'descripcion',
        'numero_cuenta',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
