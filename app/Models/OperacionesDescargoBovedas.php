<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperacionesDescargoBovedas extends Model
{
    protected $table = 'operaciones_descargo_bovedas';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'descripcion',
        'nombre_corto',
        'afecta',
        'accion',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
