<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedoresContacto extends Model
{
    protected $table = 'proveedores_contacto';
    protected $fillable = [
        'id',
        'company_id',
        'proveedor_id',
        'nombre_contacto',
        'telefono',
        'email'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Models\Proveedor', 'proveedor_id');
    }
}
