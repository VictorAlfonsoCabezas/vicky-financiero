<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SustentoTributario extends Model
{
    protected $table = 'sustento_tributario';
    protected $fillable = [
        'codigo_tributario',
        'nombre',
        'credito_tributario',
        'status'
    ];
}
