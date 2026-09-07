<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditFolderDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('credit_folder_details', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_cuota');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('code_folder_header');
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->date('date_pay')->nullable();
            $table->time('hour_pay')->nullable();
            $table->integer('user_pay_id');
            $table->date('date_vencimiento')->nullable();
            $table->decimal('interes_periodo', 8, 2)->default(0);
            $table->decimal('interes_mora', 8, 2)->default(0);
            $table->decimal('capital_amortizado', 8, 2)->default(0);
            $table->decimal('fondo_desgravamen', 8, 2)->default(0);
            $table->decimal('valor_cuota', 8, 2)->default(0);
            $table->decimal('valor_pagado', 8, 2)->default(0);
            $table->decimal('valor_final', 8, 2)->default(0);
            $table->decimal('adelanto_prox_cuota', 8, 2)->default(0);
            $table->decimal('saldo_anterior_cuota', 8, 2)->default(0);
            $table->decimal('faltante_prox_cuota', 8, 2)->default(0);
            $table->decimal('faltante_anterior_cuota', 8, 2)->default(0);
            $table->decimal('saldo_remanente', 8, 2)->default(0);
            $table->string('tipo_pago')->default('EFECTIVO');
            $table->string('obervation_pago')->nullable();
            $table->string('path')->nullable();
            $table->string('status')->default('PENDIENTE');
            $table->date('date_cancel')->nullable();
            $table->time('hour_cancel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('credit_folder_details');
    }

}
