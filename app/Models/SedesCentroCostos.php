<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SedesCentroCostos extends Model
{
    protected $table = 'sedes_centro_costos';
    protected $fillable = [
        'id',
        'company_id',
        'sede_id',
        'centro_costos_id',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function sede()
    {
        return $this->belongsTo('App\Models\Sedes', 'sede_id');
    }

    public function centroCostos()
    {
        return $this->belongsTo('App\Models\CentroCostos', 'centro_costos_id');
    }
}
