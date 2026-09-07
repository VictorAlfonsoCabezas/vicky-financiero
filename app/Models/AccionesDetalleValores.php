<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionesDetalleValores extends Model
{
    protected $table = 'acciones_detalle_valores';
    protected $fillable = [
        'id',
        'company_id',
        'acciones_header_id',
        'acciones_detalle_id',
        'acciones_valores_id',
        'valor',
        'fecha_creacion',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function accionesHeader()
    {
        return $this->belongsTo('App\Models\AccionesHeader', 'acciones_detalle_id');
    }
    public function accionesDetalle()
    {
        return $this->belongsTo('App\Models\AccionesDetalle', 'acciones_detalle_id');
    }
    public function accionesValores()
    {
        return $this->belongsTo('App\Models\AccionesValores', 'acciones_valores_id');
    }
}
