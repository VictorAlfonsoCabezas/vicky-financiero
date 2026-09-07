<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelAcademico extends Model
{
    protected $table = 'nivel_academico';
    protected $fillable = [
        'id',
        'nombre',
        'defecto',
        'status'
    ];
}
