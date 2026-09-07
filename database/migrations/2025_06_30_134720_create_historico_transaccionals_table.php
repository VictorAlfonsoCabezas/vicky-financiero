<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoTransaccionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historico_transaccionals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transacciones_cuentas_id')->unsigned();
            $table->foreign('transacciones_cuentas_id')->references('id')->on('transacciones_cuentas');
            $table->bigInteger('customer_tipo_ahorros_id')->unsigned();
            $table->foreign('customer_tipo_ahorros_id')->references('id')->on('customer_tipo_ahorros');
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->integer('type_transaction_id');
            $table->decimal('valor', 8, 2)->default('0.00');
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
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
        Schema::dropIfExists('historico_transaccionals');
    }
}
