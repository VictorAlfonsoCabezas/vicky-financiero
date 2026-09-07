<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTablePlanCuentas extends Migration
{
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            DB::table('plan_cuentas')->truncate();
            DB::table('config_plan_detalle')->truncate();
            DB::table('config_plan_header')->truncate();
            DB::table('asientos_detalle')->truncate();
            DB::table('asientos_header')->truncate();
        });
    }

    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            //
        });
    }
}
