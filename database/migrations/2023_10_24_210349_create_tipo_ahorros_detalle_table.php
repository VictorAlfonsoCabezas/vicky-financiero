<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoAhorrosDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('tipo_ahorros_detalle', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('tipo_ahorros_id');
            $table->string('afecta', 10)->nullable();
            $table->string('nombre', 50)->nullable();
            $table->decimal('valor', 8, 2)->default(0.00);
            $table->string('siglas', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_ahorros_detalle');
    }
}
