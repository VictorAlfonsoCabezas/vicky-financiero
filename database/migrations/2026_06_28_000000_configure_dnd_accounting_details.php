<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfigureDndAccountingDetails extends Migration
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

        $now = date('Y-m-d H:i:s');

        DB::table('company')
            ->select('id')
            ->orderBy('id')
            ->chunk(100, function ($companies) use ($now) {
                foreach ($companies as $company) {
                    $companyId = $company->id;

                    $transaction = DB::table('type_transactions')
                        ->where('company_id', $companyId)
                        ->where('name_corto', 'DND')
                        ->orderBy('id')
                        ->first();

                    $concepto = DB::table('conceptos')
                        ->where('company_id', $companyId)
                        ->where('nombre', 'EGRESO')
                        ->orderBy('id')
                        ->first();

                    $customerDeposit = DB::table('plan_cuentas')
                        ->where('company_id', $companyId)
                        ->where('codigo', '2.1.01.35.01')
                        ->orderBy('id')
                        ->first();

                    $adminIncome = DB::table('plan_cuentas')
                        ->where('company_id', $companyId)
                        ->where('codigo', '5.4.90.01')
                        ->orderBy('id')
                        ->first();

                    if (!$transaction || !$concepto || !$customerDeposit || !$adminIncome) {
                        continue;
                    }

                    DB::table('config_plan_header')->updateOrInsert(
                        [
                            'company_id' => $companyId,
                            'type_transaction_id' => $transaction->id,
                            'concepto_id' => $concepto->id,
                        ],
                        [
                            'operaciones_descargo_bovedas_id' => null,
                            'status' => true,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );

                    $header = DB::table('config_plan_header')
                        ->where('company_id', $companyId)
                        ->where('type_transaction_id', $transaction->id)
                        ->where('concepto_id', $concepto->id)
                        ->orderBy('id')
                        ->first();

                    if (!$header) {
                        continue;
                    }

                    DB::table('config_plan_detalle')
                        ->where('company_id', $companyId)
                        ->where('config_plan_header_id', $header->id)
                        ->delete();

                    DB::table('config_plan_detalle')->insert([
                        [
                            'company_id' => $companyId,
                            'sede_id' => null,
                            'config_plan_header_id' => $header->id,
                            'plan_cuentas_id' => $customerDeposit->id,
                            'debe_haber' => true,
                            'operacion_id' => null,
                            'campo_foraneo' => 'valor_movimiento',
                            'tabla' => null,
                            'campo' => null,
                            'status' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'company_id' => $companyId,
                            'sede_id' => null,
                            'config_plan_header_id' => $header->id,
                            'plan_cuentas_id' => $adminIncome->id,
                            'debe_haber' => false,
                            'operacion_id' => null,
                            'campo_foraneo' => 'valor_movimiento',
                            'tabla' => null,
                            'campo' => null,
                            'status' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    ]);
                }
            });
    }

    public function down()
    {
        // Data migration: do not remove accounting configuration on rollback.
    }
}
