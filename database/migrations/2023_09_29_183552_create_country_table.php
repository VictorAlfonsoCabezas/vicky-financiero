<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
    public function up()
    {
        Schema::create('country', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('codigo_pais', 10)->nullable();
            $table->string('codigo_llamada', 10)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('country');
    }
}
