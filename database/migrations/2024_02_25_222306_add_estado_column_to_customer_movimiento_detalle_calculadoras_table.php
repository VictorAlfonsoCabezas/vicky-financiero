<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoColumnToCustomerMovimientoDetalleCalculadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_movimiento_detalle_calculadoras', function (Blueprint $table) {
            $table->string('status')->after('fecha_pago')->default('PENDIENTE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_movimiento_detalle_calculadoras', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
