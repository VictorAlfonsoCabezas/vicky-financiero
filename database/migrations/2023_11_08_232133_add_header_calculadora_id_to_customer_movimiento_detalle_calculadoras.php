<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeaderCalculadoraIdToCustomerMovimientoDetalleCalculadoras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_movimiento_detalle_calculadoras', function (Blueprint $table) {
            $table->integer('header_calculadora_id')->after('customer_movimientos_id')->nullable();
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
            $table->dropColumn('header_calculadora_id');
        });
    }
}
