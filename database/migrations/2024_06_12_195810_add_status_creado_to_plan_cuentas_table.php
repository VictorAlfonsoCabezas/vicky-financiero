<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCreadoToPlanCuentasTable extends Migration
{
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->boolean('creado_usuario')->after('saldo')->default(false);
            $table->boolean('status')->after('creado_usuario')->default(true);
        });
    }

    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->dropColumn('creado_usuario');
            $table->dropColumn('status');
        });
    }
}
