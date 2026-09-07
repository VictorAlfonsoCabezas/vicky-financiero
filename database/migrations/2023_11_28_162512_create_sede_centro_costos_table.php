<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSedeCentroCostosTable extends Migration
{
    public function up()
    {
        Schema::create('sedes_centro_costos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('sede_id')->nullable();
            $table->integer('centro_costos_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sedes_centro_costos');
    }
}
