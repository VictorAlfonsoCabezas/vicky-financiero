<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTransferenciasTable extends Migration
{
   
    public function up()
    {
        Schema::create('customer_transferencias', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->datetime('fecha_creacion');
            $table->integer('customer_origen_id');
            $table->integer('customer_destino_id');
            $table->decimal('valor')->default('0.00');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_transferencias');
    }
}
