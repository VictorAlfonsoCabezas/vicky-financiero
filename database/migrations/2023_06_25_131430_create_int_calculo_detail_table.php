<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntCalculoDetailTable extends Migration
{
    public function up()
    {
        Schema::create('int_calculo_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->bigInteger('int_calculo_header_id')->unsigned();
            $table->foreign('int_calculo_header_id')->references('id')->on('int_calculo_header');
            $table->integer('customer_id')->nullable();
            $table->string('customer_code', 60)->nullable();
            $table->decimal('valor_saldo')->default('0.00');
            $table->decimal('valor_interes')->default('0.00');
            $table->decimal('valor_total')->default('0.00');
            $table->string('estado', 60)->nullable();
            $table->datetime('fecha_calculado')->nullable();
            $table->integer('user_calculado')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('int_calculo_detail');
    }
}
