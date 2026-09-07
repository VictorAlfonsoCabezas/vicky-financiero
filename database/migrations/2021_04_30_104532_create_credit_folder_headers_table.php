<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditFolderHeadersTable extends Migration {

    public function up() {
        Schema::create('credit_folder_headers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('code');
            $table->integer('customer_id');
            $table->string('customer_code');
            $table->string('customer_ruc')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_email')->nullable();
            $table->integer('customer_garante_id');
            $table->string('customer_garante_name')->nullable();
            $table->decimal('valor_solicitado', 8, 2)->default(0);
            $table->decimal('total_pagando', 8, 2)->default(0);
            $table->integer('anios_pagar')->default(0);
            $table->integer('cuotas_pagar')->default(0);
            $table->decimal('valor_cuota', 8, 2)->default(0);
            $table->decimal('valor_desgravamen', 8, 2)->default(0);
            $table->decimal('valor_interes_pago', 8, 2)->default(0);
            $table->string('tipo_pago')->default('MENSUAL');
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->date('date_verified')->nullable();
            $table->time('hour_verified')->nullable();
            $table->integer('user_verified_id')->nullable();
            $table->string('user_verified_name')->nullable();
            $table->decimal('valor_encaje', 8, 2)->default(0);
            $table->string('path_encaje')->nullable();
            $table->date('date_cancel')->nullable();
            $table->time('hour_cancel')->nullable();
            $table->string('user_cancel')->nullable();
            $table->integer('user_cancel_id')->nullable();
            $table->string('user_cancel_name')->nullable();
            $table->string('status', 20)->default('PENDIENTE');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('credit_folder_headers');
    }

}
