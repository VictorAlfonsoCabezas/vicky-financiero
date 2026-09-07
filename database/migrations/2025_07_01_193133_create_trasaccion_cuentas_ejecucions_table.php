<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrasaccionCuentasEjecucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trasaccion_cuentas_ejecucions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transacciones_cuentas_id')->unsigned();
            $table->foreign('transacciones_cuentas_id')->references('id')->on('transacciones_cuentas');
            $table->integer('month');
            $table->integer('year');
            $table->date('fecha_inicial');
            $table->date('fecha_final');
            $table->decimal('valor_debito', 8, 2)->default('0.00');
            $table->decimal('valor_multa', 8, 2)->default('0.00');
            $table->date('date_created');
            $table->time('hour_created');
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
        Schema::dropIfExists('trasaccion_cuentas_ejecucions');
    }
}
