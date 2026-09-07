<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrasaccionCuentasEjecucionsIdColumnToHistoricoTransaccionals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historico_transaccionals', function (Blueprint $table) {
            $table->integer('trasaccion_cuentas_ejecucions_id')->after('transacciones_cuentas_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historico_transaccionals', function (Blueprint $table) {
            $table->dropColumn('trasaccion_cuentas_ejecucions_id');
        });
    }
}
