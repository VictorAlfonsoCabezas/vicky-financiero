<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountingIndexes extends Migration
{
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->index(['company_id', 'codigo'], 'idx_plan_cuentas_company_codigo');
            $table->index(['company_id', 'estado_resultados'], 'idx_plan_cuentas_company_resultados');
        });

        Schema::table('asientos_header', function (Blueprint $table) {
            $table->index(['company_id', 'fecha_contable', 'status'], 'idx_asientos_header_company_fecha');
        });

        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->index(['company_id', 'asientos_header_id'], 'idx_asientos_detalle_company_header');
            $table->index(['company_id', 'plan_cuentas_id'], 'idx_asientos_detalle_company_plan');
        });

        Schema::table('config_plan_detalle', function (Blueprint $table) {
            $table->index(['company_id', 'config_plan_header_id'], 'idx_config_plan_detalle_company_header');
            $table->index(['company_id', 'plan_cuentas_id'], 'idx_config_plan_detalle_company_plan');
        });
    }

    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->dropIndex('idx_plan_cuentas_company_codigo');
            $table->dropIndex('idx_plan_cuentas_company_resultados');
        });

        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropIndex('idx_asientos_header_company_fecha');
        });

        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->dropIndex('idx_asientos_detalle_company_header');
            $table->dropIndex('idx_asientos_detalle_company_plan');
        });

        Schema::table('config_plan_detalle', function (Blueprint $table) {
            $table->dropIndex('idx_config_plan_detalle_company_header');
            $table->dropIndex('idx_config_plan_detalle_company_plan');
        });
    }
}
