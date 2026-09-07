<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastosImpuestos extends Model
{
    protected $table = 'gastos_impuestos';
    protected $fillable = [
        'id',
        'gastos_id',
        'impuestos_id',
        'subtotal',
        'iva'
    ];

    public function gastos()
    {
        return $this->belongsTo('App\Models\Gastos', 'gastos_id');
    }

    public function impuestos()
    {
        return $this->belongsTo('App\Models\Impuestos', 'impuestos_id');
    }
}
