<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerMovimientoDetalleCalculadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_movimiento_detalle_calculadoras', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_movimientos_id');
            $table->decimal('rentabilidad', 8, 2)->nullable();
            $table->decimal('penalizado', 8, 2)->nullable();
            $table->decimal('total', 8, 2)->nullable();
            $table->date('fecha_pago')->nullable();
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
        Schema::dropIfExists('customer_movimiento_detalle_calculadoras');
    }
}
