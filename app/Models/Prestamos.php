<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PlanCuentas;

class Prestamos extends Model
{

    protected $table = "prestamos";
    protected $fillable = [
        'id',
        'company_id',
        'calculo_simple',
        'name',
        'interes',
        'fondo_desgravamen',
        'valor_maximo',
        'valor_minimo',
        'tipo',
        'status',
        'diario',
        'edad_maxima',
        'edad_minima',
        'periodo_id',
        'interes_anual',
        'letra_cambio',
        'plan_cuenta_id',
        'pagare',
        'contrato',
        'administrativo_porcentaje_valor',
        'gasto_administrativo',
        'encaje',
        'encaje_credito_cuenta',
        'encaje_porcentaje_valor',
        'encaje_cantidad',
        'primer_gasto',
        'segundo_gasto',
        'tercer_gasto',
        'ahorro',
        'suma_valores_gastos_prestamo',
        'porcentaje_primer_gasto',
        'porcentaje_segundo_gasto',
        'porcentaje_tercer_gasto',
        'letra_credito',
        'lleva_contabilidad'
    ];

    public function planCuenta()
    {
        return $this->belongsTo(PlanCuentas::class, 'plan_cuenta_id');
    }

}
