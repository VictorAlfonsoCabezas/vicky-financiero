<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionesValores extends Model
{
    protected $table = 'acciones_valores';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'fecha_creacion',
        'descripcion',
        'tipo',
        'bloqueado',
        'operacion',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
