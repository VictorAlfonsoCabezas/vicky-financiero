<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaRetencion extends Model
{
    protected $table = 'lista_retencion';
    protected $fillable = [
        'id',
        'nombre',
        'codigo',
        'porcentaje',
        'tipo_retencion_id',
        'status'
    ];

    public function tipoRetencion()
    {
        return $this->belongsTo('App\Models\TipoRetencion', 'tipo_retencion_id');
    }
}
