<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model {

    protected $table = "rol";
    protected $fillable = [
        'nombre',
        'observation',
        'user_create',
        'status'
    ];

}
