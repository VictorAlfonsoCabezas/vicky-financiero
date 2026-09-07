<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionesDetalle extends Model
{
    protected $table = 'acciones_detalle';
    protected $fillable = [
        'id',
        'company_id',
        'acciones_header_id',
        'customer_id',
        'descripcion',
        'porcentaje',
        'valor',
        'user_created',
        'status',
        'hora_entrega',
        'fecha_entrega',
        'user_entrega_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function accionesHeader()
    {
        return $this->belongsTo('App\Models\AccionesHeader', 'acciones_header_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }
}
