<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToCustumerMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_forma_Pago')->nullable()->after('id');
            $table->unsignedBigInteger('gastos_plan_cuentas_id')->nullable()->after('id_forma_Pago');
            $table->unsignedBigInteger('gastos_centro_costos_id')->nullable()->after('gastos_plan_cuentas_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn([
                'id_forma_Pago',
                'gastos_plan_cuentas_id',
                'gastos_centro_costos_id'
            ]);
        });
    }
}
