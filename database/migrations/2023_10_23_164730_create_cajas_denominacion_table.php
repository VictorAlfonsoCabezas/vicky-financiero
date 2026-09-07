<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasDenominacionTable extends Migration
{
    public function up()
    {
        Schema::create('cajas_denominacion', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('caja_id');
            $table->integer('denominacion_billetes_id');
            $table->integer('cantidad');
            $table->decimal('total');
            $table->datetime('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas_denominacion');
    }
}
