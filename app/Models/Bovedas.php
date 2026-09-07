<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bovedas extends Model
{
    protected $table = 'bovedas';
    protected $fillable = [
        'id',
        'company_id',
        'fecha_creacion',
        'nombre',
        'descripcion',
        'principal',
        'caja',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
