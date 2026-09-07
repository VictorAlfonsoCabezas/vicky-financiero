<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccionesDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('acciones_detalle', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('acciones_header_id');
            $table->integer('customer_id')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->decimal('porcentaje')->default('0.00');
            $table->decimal('valor')->default('0.00');
            $table->integer('user_created')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acciones_detalle');
    }
}
