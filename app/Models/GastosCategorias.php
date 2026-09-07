<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastosCategorias extends Model
{
    protected $table = 'gastos_categorias';
    protected $fillable = [
        'id',
        'company_id',
        'nombre',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
