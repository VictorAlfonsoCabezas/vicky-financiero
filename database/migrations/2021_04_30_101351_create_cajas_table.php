<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration {

    public function up() {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('code', 60);
            $table->decimal('valor_inicial', 8, 2)->default(0);
            $table->date('date_inicial')->nullable();
            $table->time('hour_inicial')->nullable();
            $table->integer('user_inicial_id')->nullable();
            $table->string('user_name_inicial')->nullable();
            $table->integer('numero_movimientos')->default(0);
            $table->integer('numero_movimientos_final')->nullable()->default(0);
            $table->decimal('valor_ingreso_clientes', 8, 2)->default(0);
            $table->decimal('valor_ingreso_clientes_final', 8, 2)->default(0);
            $table->decimal('valor_egreso_clientes', 8, 2)->default(0);
            $table->decimal('valor_egreso_clientes_final', 8, 2)->default(0);
            $table->decimal('valor_ingreso_cajas', 8, 2)->default(0);
            $table->decimal('valor_ingreso_cajas_final', 8, 2)->default(0);
            $table->decimal('valor_egreso_cajas', 8, 2)->default(0);
            $table->decimal('valor_egreso_cajas_final', 8, 2)->default(0);
            $table->decimal('total', 8, 2)->default(0);
            $table->decimal('total_final', 8, 2)->default(0);
            $table->date('date_finish')->nullable();
            $table->time('hour_finish')->nullable();
            $table->integer('user_finish_id')->nullable();
            $table->string('user_finish_name')->nullable();
            $table->string('cierre', 20)->default('AUTOMATICO');
            $table->string('type_cierre', 20)->default('HIJO');
            $table->string('cierre_padre', 9)->nullable();
            $table->decimal('descuadre', 8, 2)->default(0);
            $table->string('status', 20)->default('ABIERTA');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('cajas');
    }

}
