<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteraEnviosHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('cartera_envios_header', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('cartera_reglas_id');
            $table->dateTime('fecha_creacion');
            $table->string('anio', 5);
            $table->string('mes', 5);
            $table->integer('user_created');
            $table->integer('user_calcuado');
            $table->string('status', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cartera_envios_header');
    }
}
