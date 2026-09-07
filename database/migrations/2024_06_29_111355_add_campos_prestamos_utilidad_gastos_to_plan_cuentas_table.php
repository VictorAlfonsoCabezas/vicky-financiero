<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposPrestamosUtilidadGastosToPlanCuentasTable extends Migration
{
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->boolean('prestamo')->after('saldo')->default(false);
            $table->integer('tiempo_inicio')->after('prestamo')->nullable();
            $table->integer('tiempo_fin')->after('tiempo_inicio')->nullable();
            $table->boolean('gasto')->after('tiempo_fin')->default(false);
            $table->boolean('utilidad')->after('gasto')->default(false);
        });
    }

    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->dropColumn('prestamo');
            $table->dropColumn('tiempo_inicio');
            $table->dropColumn('tiempo_fin');
            $table->dropColumn('gasto');
            $table->dropColumn('utilidad');
        });
    }
}
