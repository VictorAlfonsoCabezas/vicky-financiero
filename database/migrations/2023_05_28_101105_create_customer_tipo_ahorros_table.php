<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTipoAhorrosTable extends Migration
{
    public function up()
    {
        Schema::create('customer_tipo_ahorros', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('tipo_ahorros_id');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_tipo_ahorros');
    }
}
