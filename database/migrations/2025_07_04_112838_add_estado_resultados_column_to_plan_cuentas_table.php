<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoResultadosColumnToPlanCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->boolean('estado_resultados')->default(false)->after('creado_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->dropColumn('estado_resultados');
        });
    }
}
