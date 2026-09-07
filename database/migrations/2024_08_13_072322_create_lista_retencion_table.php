<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListaRetencionTable extends Migration
{
    public function up()
    {
        Schema::create('lista_retencion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 12);
            $table->float('porcentaje');
            $table->integer('tipo_retencion_id');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lista_retencion');
    }
}
