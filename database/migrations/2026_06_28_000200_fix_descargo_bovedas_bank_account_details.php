<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixDescargoBovedasBankAccountDetails extends Migration
{
    public function up()
    {
        if (
            !Schema::hasTable('company') ||
            !Schema::hasTable('conceptos') ||
            !Schema::hasTable('operaciones_descargo_bovedas') ||
            !Schema::hasTable('plan_cuentas') ||
            !Schema::hasTable('config_plan_header') ||
            !Schema::hasTable('config_plan_detalle')
        ) {
            return;
        }

        DB::table('company')
            ->select('id')
            ->orderBy('id')
            ->chunk(100, function ($companies) {
                foreach ($companies as $company) {
                    $this->configureCompany((int) $company->id);
                }
            });
    }

    private function configureCompany($companyId)
    {
        $diario = $this->concepto($companyId, 'DIARIO');
        $egreso = $this->concepto($companyId, 'EGRESO');

        $transitory = $this->plan($companyId, '2.9.90.90.25');
        $customerDeposit = $this->plan($companyId, '2.1.01.35.01');
        $bankPlans = $this->bankPlans($companyId);

        if ($diario && $transitory && !empty($bankPlans)) {
            $this->syncOperation($companyId, 'CAREM', $diario->id, array_merge(
                $this->detailsForPlans($bankPlans, true),
                [$this->detail($transitory->id, false)]
            ));
        }

        if ($egreso && $customerDeposit && !empty($bankPlans)) {
            $this->syncOperation($companyId, 'CARCLI', $egreso->id, array_merge(
                $this->detailsForPlans($bankPlans, true),
                [$this->detail($customerDeposit->id, false)]
            ));
        }
    }

    private function syncOperation($companyId, $operationShortName, $conceptoId, array $details)
    {
        $operation = DB::table('operaciones_descargo_bovedas')
            ->where('company_id', $companyId)
            ->where('nombre_corto', $operationShortName)
            ->orderBy('id')
            ->first();

        if (!$operation) {
            return;
        }

        $header = DB::table('config_plan_header')
            ->where('company_id', $companyId)
            ->where('operaciones_descargo_bovedas_id', $operation->id)
            ->where('concepto_id', $conceptoId)
            ->orderBy('id')
            ->first();

        if (!$header) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        DB::table('config_plan_header')
            ->where('id', $header->id)
            ->update([
                'status' => true,
                'updated_at' => $now,
            ]);

        DB::table('config_plan_detalle')
            ->where('company_id', $companyId)
            ->where('config_plan_header_id', $header->id)
            ->delete();

        foreach ($details as $detail) {
            DB::table('config_plan_detalle')->insert(array_merge($detail, [
                'company_id' => $companyId,
                'sede_id' => null,
                'config_plan_header_id' => $header->id,
                'operacion_id' => null,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function detailsForPlans(array $plans, $debeHaber)
    {
        return array_map(function ($plan) use ($debeHaber) {
            return $this->detail($plan->id, $debeHaber);
        }, $plans);
    }

    private function detail($planCuentasId, $debeHaber)
    {
        return [
            'plan_cuentas_id' => $planCuentasId,
            'debe_haber' => $debeHaber,
            'campo_foraneo' => 'valor',
            'tabla' => null,
            'campo' => null,
        ];
    }

    private function concepto($companyId, $nombre)
    {
        return DB::table('conceptos')
            ->where('company_id', $companyId)
            ->where('nombre', $nombre)
            ->orderBy('id')
            ->first();
    }

    private function plan($companyId, $codigo)
    {
        return DB::table('plan_cuentas')
            ->where('company_id', $companyId)
            ->where('codigo', $codigo)
            ->where('status', true)
            ->orderBy('id')
            ->first();
    }

    private function bankPlans($companyId)
    {
        $plans = DB::table('plan_cuentas')
            ->where('company_id', $companyId)
            ->where('status', true)
            ->whereNotNull('banco_id')
            ->where('codigo', 'like', '1.1.03.%')
            ->orderBy('id')
            ->get()
            ->all();

        if (!empty($plans)) {
            return $plans;
        }

        $fallback = $this->plan($companyId, '1.1.03.05');

        return $fallback ? [$fallback] : [];
    }

    public function down()
    {
        // Data migration: do not remove accounting configuration on rollback.
    }
}
