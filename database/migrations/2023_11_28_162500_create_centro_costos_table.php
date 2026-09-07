<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentroCostosTable extends Migration
{
    public function up()
    {
        Schema::create('centro_costos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('code', 10)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('centro_costos');
    }
}
