<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsientosDetalle extends Model
{
    protected $table = 'asientos_detalle';
    protected $fillable = [
        'company_id',
        'asientos_header_id',
        'plan_cuentas_id',
        'sedes_centro_costos_id',
        'valor',
        'debe_haber',
        'fecha_contable',
        'fecha_creacion',
        'user_created',
        'observacion',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
    public function asientos()
    {
        return $this->belongsTo('App\Models\AsientosHeader', 'asientos_header_id');
    }
    public function planCuentas()
    {
        return $this->belongsTo('App\Models\PlanCuentas', 'plan_cuentas_id');
    }
    public function sedesCentroCostos()
    {
        return $this->belongsTo('App\Models\SedesCentroCostos', 'sedes_centro_costos_id');
    }
    public function userCreated()
    {
        return $this->belongsTo('App\User', 'user_created');
    }
}
