<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGastosFormaPagosIdToCustomerMovimientosTable extends Migration
{
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->integer('gastos_forma_pagos_id')->nullable()->after('gastos_id');
        });
    }

    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('gastos_forma_pagos_id');
        });
    }
}
