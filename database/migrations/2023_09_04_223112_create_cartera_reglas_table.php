<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteraReglasTable extends Migration
{
    public function up()
    {
        Schema::create('cartera_reglas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre', 60)->nullable();
            $table->string('mensaje', 1200)->nullable();
            $table->integer('dias_mora')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cartera_reglas');
    }
}
