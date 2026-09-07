<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    protected $table = 'proveedores';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'descripcion',
        'ruc',
        'telefono',
        'email',
        'plan_cuenta_id',
        'centro_costos_id',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
