<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{
    protected $table = 'parroquia';
    protected $fillable = [
        'id',
        'nombre',
        'defecto',
        'provincia_id',
        'status',
    ];
}
