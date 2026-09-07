<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteraEnviosDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('cartera_envios_detalle', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('cartera_envios_header_id');
            $table->integer('credit_folder_detalle_id');
            $table->string('mensaje', 1200);
            $table->string('estado', 15);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cartera_envios_detalle');
    }
}
