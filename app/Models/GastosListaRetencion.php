<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastosListaRetencion extends Model
{
    protected $table = 'gastos_lista_retencion';
    protected $fillable = [
        'id',
        'gastos_id',
        'lista_retencion_id',
        'valor',
        'calculo'
    ];

    public function gastos()
    {
        return $this->belongsTo('App\Models\Gastos', 'gastos_id');
    }

    public function listaRetencion()
    {
        return $this->belongsTo('App\Models\ListaRetencion', 'lista_retencion_id');
    }
}
