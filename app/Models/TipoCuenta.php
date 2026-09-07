<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCuenta extends Model
{
    protected $table = 'tipo_cuenta';
    protected $fillable = [
        'id',
        'nombre',
        'apellido',
        'status'
    
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }  

}

