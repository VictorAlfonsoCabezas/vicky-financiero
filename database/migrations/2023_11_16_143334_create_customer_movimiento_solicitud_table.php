<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerMovimientoSolicitudTable extends Migration
{
    public function up()
    {
        Schema::create('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('customer_id');
            $table->integer('user_id');
            $table->datetime('fecha_creacion');
            $table->decimal('valor', 8, 2)->default(0.00);
            $table->string('observacion')->nullable();
            $table->string('estado')->default('PENDIENTE');
            $table->datetime('fecha_rechazado')->nullable();
            $table->string('razon_rechazado')->nullable();
            $table->integer('user_rechazado')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_movimiento_solicitud');
    }
}
