<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntReglasTable extends Migration
{
    public function up()
    {
        Schema::create('int_reglas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('nombre', 100)->nullable();
            $table->string('texto', 60)->nullable();
            $table->string('description', 255)->nullable();
            $table->datetime('fecha_inicio')->nullable();
            $table->integer('tipo_ahorro_id')->nullable();
            $table->decimal('interes')->default('0.00');
            $table->decimal('valor')->default('0.00');
            $table->string('operacion')->default('(-)');
            $table->integer('rango_valor')->nullable();
            $table->string('rango_tiempo', 20)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('int_reglas');
    }
}
