<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosListaRetencionTable extends Migration
{
    public function up()
    {
        Schema::create('gastos_lista_retencion', function (Blueprint $table) {
            $table->id();
            $table->integer('gasto_id')->nullable();
            $table->integer('lista_retencion_id')->nullable();
            $table->decimal('valor')->default('0.00');
            $table->decimal('calculo')->default('0.00');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos_lista_retencion');
    }
}
