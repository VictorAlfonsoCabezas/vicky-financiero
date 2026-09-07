<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigPlanDetalle extends Model
{
    protected $table = 'config_plan_detalle';
    protected $fillable = [
        'id',
        'company_id',
        'sede_id',
        'config_plan_header_id',
        'plan_cuentas_id',
        'debe_haber',
        'operacion_id',
        'campo_foraneo',
        'tabla',
        'campo',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function sede()
    {
        return $this->belongsTo('App\Models\Sedes', 'sede_id');
    }

    public function planCuentas()
    {
        return $this->belongsTo('App\Models\PlanCuentas', 'plan_cuentas_id');
    }

    public function operacion()
    {
        return $this->belongsTo('App\Models\Operacion', 'operacion_id');
    }

    public function header()
    {
        return $this->belongsTo('App\Models\ConfigPlanHeader', 'config_plan_header_id');
    }
}
