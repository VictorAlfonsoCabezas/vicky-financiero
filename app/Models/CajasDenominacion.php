<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajasDenominacion extends Model
{
    protected $table = 'cajas_denominacion';
    protected $fillable = [
        'company_id',
        'caja_id',
        'denominacion_billetes_id',
        'cantidad',
        'total',
        'fecha_creacion',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function caja()
    {
        return $this->belongsTo('App\Models\Cajas', 'caja_id');
    }

    public function denomianciones()
    {
        return $this->belongsTo('App\Models\DenominacionBilletes', 'denominacion_billetes_id');
    }
}
