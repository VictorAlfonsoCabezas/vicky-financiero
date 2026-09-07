<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroFormasPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_formas_pagos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('customer_movimientos_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('letra_id')->nullable();
            $table->integer('deposito_id')->nullable();
            $table->boolean('liquidacion')->default(false);
            $table->integer('prestamo_id')->nullable();
            $table->string('forma_pago')->nullable();
            $table->integer('forma_pago_id')->nullable();
            $table->decimal('valor', 8, 2)->default(0);
            $table->date('date_create')->nullable();
            $table->time('hour_create')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_formas_pagos');
    }
}
