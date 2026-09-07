<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosImpuestosTable extends Migration
{
    public function up()
    {
        Schema::create('gastos_impuestos', function (Blueprint $table) {
            $table->id();
            $table->integer('gastos_id');
            $table->integer('impuestos_id');
            $table->decimal('subtotal', 8, 2)->default('0.00');
            $table->decimal('iva', 8, 2)->default('0.00');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos_impuestos');
    }
}
