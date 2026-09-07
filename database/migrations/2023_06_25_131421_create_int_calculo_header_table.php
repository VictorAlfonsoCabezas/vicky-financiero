<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntCalculoHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('int_calculo_header', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->bigInteger('int_reglas_id')->unsigned();
            $table->foreign('int_reglas_id')->references('id')->on('int_reglas');
            $table->datetime('fecha_creacion')->nullable();
            $table->string('anio', 5)->nullable();
            $table->string('mes', 5)->nullable();
            $table->integer('clientes_total')->nullable();
            $table->integer('clientes_excluidos')->nullable();
            $table->integer('clientes_calculados')->nullable();
            $table->integer('user_created')->nullable();
            $table->integer('user_calculado')->nullable();
            $table->string('estado', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('int_calculo_header');
    }
}
