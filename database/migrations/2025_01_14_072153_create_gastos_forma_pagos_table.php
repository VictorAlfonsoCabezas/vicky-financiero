<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosFormaPagosTable extends Migration
{
    public function up()
    {
        Schema::create('gastos_forma_pagos', function (Blueprint $table) {
            $table->id();
            $table->integer('gasto_id');
            $table->integer('forma_pago_id');
            $table->decimal('valor', 8, 2)->default('0.00');
            $table->dateTime('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos_forma_pagos');
    }
}
