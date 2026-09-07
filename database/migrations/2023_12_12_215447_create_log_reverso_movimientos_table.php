<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogReversoMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_reverso_movimientos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->date('date_create')->nullable();
            $table->time('hour_create')->nullable();
            $table->string('detalle')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->date('date_movimiento')->nullable();
            $table->time('hour_movimiento')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('observacion')->nullable();
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
        Schema::dropIfExists('log_reverso_movimientos');
    }
}
