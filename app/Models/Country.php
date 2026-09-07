<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';
    protected $fillable = [
        'id',
        'nombre',
        'defecto',
        'codigo_pais',
        'codigo_llamada',
        'status'
    ];
}
