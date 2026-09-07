<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionesCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transacciones_cuentas', function (Blueprint $table) {
            $table->id();
            $table->string('concepto');
            $table->integer('dia_mes')->default(1);
            $table->boolean('porcentaje_recaudacion')->default(false);
            $table->decimal('valor_recaudacion', 8, 2)->default('0.00');
            $table->integer('dias_multa')->default(0);
            $table->boolean('porcentaje_multa')->default(false);
            $table->decimal('valor_multa', 8, 2)->default('0.00');
            $table->integer('tipo_ahorro_id');
            $table->integer('banco_id');
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
        Schema::dropIfExists('transacciones_cuentas');
    }
}
