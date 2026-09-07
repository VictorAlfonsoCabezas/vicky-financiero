<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixCreditPaymentAccountingDetails extends Migration
{
    public function up()
    {
        if (
            !Schema::hasTable('company') ||
            !Schema::hasTable('conceptos') ||
            !Schema::hasTable('type_transactions') ||
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
        $concepto = $this->concepto($companyId, 'INGRESOS');
        $cash = $this->plan($companyId, '1.1.01.05.01', 'CAJA GENERAL', ['prestamo' => true]);
        $interestIncome = $this->plan($companyId, '5.1.04.10', 'INTERESES CARTERA DE CREDITO', ['estado_resultados' => true]);
        $moraIncome = $this->plan($companyId, '5.1.04.50', 'DE MORA', ['estado_resultados' => true, 'prestamo' => true]);
        $loanPlans = $this->loanPlans($companyId);
        $bankOrCashDetails = array_merge([$cash], $this->bankPlans($companyId));

        if (!$concepto || !$cash || !$interestIncome || !$moraIncome || empty($loanPlans)) {
            return;
        }

        foreach (['PC', 'PCA', 'PCI'] as $shortName) {
            $transaction = DB::table('type_transactions')
                ->where('company_id', $companyId)
                ->where('name_corto', $shortName)
                ->orderBy('id')
                ->first();

            if (!$transaction) {
                continue;
            }

            $header = $this->header($companyId, $concepto->id, $transaction->id);
            $this->syncDetails($companyId, $header->id, array_merge(
                $this->detailsForPlans($bankOrCashDetails, true, 'valor_movimiento'),
                $this->detailsForPlans($loanPlans, false, 'credit_folder_details_id', 'credit_folder_details', 'capital_amortizado'),
                [
                    $this->detail($interestIncome->id, false, 'credit_folder_details_id', 'credit_folder_details', 'interes_periodo'),
                    $this->detail($moraIncome->id, false, 'credit_folder_details_id', 'credit_folder_details', 'interes_mora'),
                ]
            ));
        }
    }

    private function header($companyId, $conceptoId, $transactionId)
    {
        $now = date('Y-m-d H:i:s');

        DB::table('config_plan_header')->updateOrInsert(
            [
                'company_id' => $companyId,
                'type_transaction_id' => $transactionId,
                'concepto_id' => $conceptoId,
            ],
            [
                'operaciones_descargo_bovedas_id' => null,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return DB::table('config_plan_header')
            ->where('company_id', $companyId)
            ->where('type_transaction_id', $transactionId)
            ->where('concepto_id', $conceptoId)
            ->orderBy('id')
            ->first();
    }

    private function syncDetails($companyId, $headerId, array $details)
    {
        $now = date('Y-m-d H:i:s');

        DB::table('config_plan_detalle')
            ->where('company_id', $companyId)
            ->where('config_plan_header_id', $headerId)
            ->delete();

        foreach ($details as $detail) {
            DB::table('config_plan_detalle')->insert(array_merge($detail, [
                'company_id' => $companyId,
                'sede_id' => null,
                'config_plan_header_id' => $headerId,
                'operacion_id' => null,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function detailsForPlans(array $plans, $debeHaber, $campoForaneo = 'valor_movimiento', $tabla = null, $campo = null)
    {
        return array_map(function ($plan) use ($debeHaber, $campoForaneo, $tabla, $campo) {
            return $this->detail($plan->id, $debeHaber, $campoForaneo, $tabla, $campo);
        }, $plans);
    }

    private function detail($planCuentasId, $debeHaber, $campoForaneo = 'valor_movimiento', $tabla = null, $campo = null)
    {
        return [
            'plan_cuentas_id' => $planCuentasId,
            'debe_haber' => $debeHaber,
            'campo_foraneo' => $campoForaneo,
            'tabla' => $tabla,
            'campo' => $campo,
        ];
    }

    private function concepto($companyId, $nombre)
    {
        return DB::table('conceptos')
            ->where('company_id', $companyId)
            ->where('nombre', $nombre)
            ->where('status', true)
            ->orderBy('id')
            ->first();
    }

    private function plan($companyId, $codigo, $nombre, array $attributes = [])
    {
        $now = date('Y-m-d H:i:s');
        $plan = DB::table('plan_cuentas')
            ->where('company_id', $companyId)
            ->where('codigo', $codigo)
            ->orderBy('id')
            ->first();

        $data = array_merge([
            'nombre' => $plan && $plan->nombre ? $plan->nombre : $nombre,
            'status' => true,
            'updated_at' => $now,
        ], $this->levels($codigo), $attributes);

        if ($plan) {
            DB::table('plan_cuentas')
                ->where('id', $plan->id)
                ->update($data);

            return DB::table('plan_cuentas')->where('id', $plan->id)->first();
        }

        $id = DB::table('plan_cuentas')->insertGetId(array_merge($data, [
            'company_id' => $companyId,
            'codigo' => $codigo,
            'saldo' => 0,
            'creado_usuario' => true,
            'created_at' => $now,
        ]));

        return DB::table('plan_cuentas')->where('id', $id)->first();
    }

    private function loanPlans($companyId)
    {
        return [
            $this->plan($companyId, '1.4.02.05', 'CARTERA DE CREDITO DE 1 A 30 DIAS', ['prestamo' => true, 'tiempo_inicio' => 1, 'tiempo_fin' => 30]),
            $this->plan($companyId, '1.4.02.10', 'CARTERA DE CREDITO DE 31 A 90 DIAS', ['prestamo' => true, 'tiempo_inicio' => 31, 'tiempo_fin' => 90]),
            $this->plan($companyId, '1.4.02.15', 'CARTERA DE CREDITO DE 91 A 180 DIAS', ['prestamo' => true, 'tiempo_inicio' => 91, 'tiempo_fin' => 180]),
            $this->plan($companyId, '1.4.02.20', 'CARTERA DE CREDITO DE 181 A 360 DIAS', ['prestamo' => true, 'tiempo_inicio' => 181, 'tiempo_fin' => 360]),
            $this->plan($companyId, '1.4.02.25', 'CARTERA DE CREDITO DE MAS DE 360 DIAS', ['prestamo' => true, 'tiempo_inicio' => 361, 'tiempo_fin' => null]),
        ];
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

        return [$this->plan($companyId, '1.1.03.05', 'BANCO PRINCIPAL')];
    }

    private function levels($codigo)
    {
        $parts = explode('.', trim($codigo, '.'));
        $levels = [];

        for ($i = 1; $i <= 7; $i++) {
            $levels['nivel' . $i] = $parts[$i - 1] ?? null;
        }

        return $levels;
    }

    public function down()
    {
        // Data migration: keep accounting configuration in place on rollback.
    }
}
