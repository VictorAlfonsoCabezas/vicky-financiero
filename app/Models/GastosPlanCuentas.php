<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastosPlanCuentas extends Model
{
    protected $table = 'gastos_plan_cuentas';
    protected $fillable = [
        'id',
        'company_id',
        'gasto_id',
        'plan_cuentas_id',
        'centro_costos_id',
        'valor',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function gastos()
    {
        return $this->belongsTo('App\Models\Gastos', 'gasto_id');
    }

    public function planCuentas()
    {
        return $this->belongsTo('App\Models\PlanCuentas', 'plan_cuentas_id');
    }

    public function centroCostos()
    {
        return $this->belongsTo('App\Models\CentroCostos', 'centro_costos_id');
    }
    
}
