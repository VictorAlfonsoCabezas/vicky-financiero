<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudad';
    protected $fillable = [
        'id',
        'nombre',
        'defecto',
        'parroquia_id',
        'status'
    ];

}
