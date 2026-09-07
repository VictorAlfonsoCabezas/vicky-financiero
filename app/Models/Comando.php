<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Comando extends Model
{
    protected $table = 'comando';
    protected $fillable = [
        'id',
        'nombre',  
    ];

}
