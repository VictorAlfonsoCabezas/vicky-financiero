<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IncreaseAccountingDecimalPrecision extends Migration
{
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->decimal('saldo', 18, 2)->default(0.00)->change();
        });

        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->decimal('valor', 18, 2)->default(0.00)->change();
        });

        DB::statement('ALTER TABLE gastos_plan_cuentas MODIFY valor DECIMAL(18, 2) NULL');
    }

    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->decimal('saldo', 8, 2)->default(0.00)->change();
        });

        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->default(0.00)->change();
        });

        DB::statement('ALTER TABLE gastos_plan_cuentas MODIFY valor DECIMAL(8, 2) NULL');
    }
}
