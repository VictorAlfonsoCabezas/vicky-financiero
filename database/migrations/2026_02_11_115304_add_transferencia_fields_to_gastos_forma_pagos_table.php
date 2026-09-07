<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferenciaFieldsToGastosFormaPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gastos_forma_pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('banco_id')->nullable();
            $table->string('numero_comprobante')->nullable();
        });
    }

    public function down()
    {
        Schema::table('gastos_forma_pagos', function (Blueprint $table) {
            $table->dropColumn(['banco_id', 'numero_comprobante']);
        });
    }
}
