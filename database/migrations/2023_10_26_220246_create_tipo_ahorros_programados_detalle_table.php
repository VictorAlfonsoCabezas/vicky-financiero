<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoAhorrosProgramadosDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('tipo_ahorros_programados_detalle', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('tipo_ahorros_id');
            $table->decimal('interes', 8, 2)->nullable();
            $table->integer('rango_min')->nullable();
            $table->integer('rango_max')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_ahorros_programados_detalle');
    }
}
