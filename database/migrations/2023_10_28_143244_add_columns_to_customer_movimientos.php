<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCustomerMovimientos extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->integer('tipo_ahorros_programados_detalle_id')->after('automatico')->nullable();
            $table->integer('plazo_dias_programado')->after('automatico')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('tipo_ahorros_programados_detalle_id');
            $table->dropColumn('plazo_dias_programado');
        });
    }

}
