<?php

namespace Tests\Concerns;

use App\Models\{AsientosDetalle, ConfigPlanDetalle, ConfigPlanHeader, Company, Conceptos, CreditFolderHeader, PlanCuentas, Utilidad};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait ContabilidadDePrueba
{
    private function habilitarContabilidadDePrueba($conDetalle = true)
    {
        foreach ([AsientosDetalle::class, ConfigPlanDetalle::class, ConfigPlanHeader::class, PlanCuentas::class, Utilidad::class, \App\User::class] as $class) {
            $model = new $class;
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->increments('id');
                foreach (array_unique($model->getFillable()) as $field) {
                    if ($field !== 'id') $table->string($field)->nullable();
                }
                $table->timestamps();
            });
        }
        Company::where('id', 10)->update(['contabilidad' => 1, 'fecha_inicio_contable' => '2026-01-01']);
        CreditFolderHeader::where('id', 1)->update(['tipo_pago' => 'MENSUAL', 'cuotas_pagar' => 12]);
        Conceptos::create(['id' => 1, 'company_id' => 10, 'nombre' => 'DIARIO']);
        Conceptos::create(['id' => 2, 'company_id' => 10, 'nombre' => 'EGRESO']);
        if ($conDetalle) {
            ConfigPlanHeader::create(['id' => 1, 'company_id' => 10, 'type_transaction_id' => 1, 'concepto_id' => 1, 'status' => 1]);
            PlanCuentas::create(['id' => 1, 'company_id' => 10, 'codigo' => '1.1.03.01', 'banco_id' => 1]);
            // Solo Debe: reproduce un asiento descuadrado real sin datos invalidos.
            ConfigPlanDetalle::create(['company_id' => 10, 'config_plan_header_id' => 1,
                'plan_cuentas_id' => 1, 'debe_haber' => 1, 'campo_foraneo' => 'valor_movimiento', 'status' => 1]);
        }
    }
}
