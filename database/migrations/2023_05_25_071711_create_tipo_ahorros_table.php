<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoAhorrosTable extends Migration
{
    public function up()
    {
        Schema::create('tipo_ahorros', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('name', 60);
            $table->string('description', 255);
            $table->integer('edad_min')->nullable();
            $table->integer('edad_max')->nullable();
            $table->decimal('interes', 8, 2)->default(0.00);
            $table->integer('rango_valor')->default(1);
            $table->string('rango_tiempo', 10)->default('month');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_ahorros');
    }
}
