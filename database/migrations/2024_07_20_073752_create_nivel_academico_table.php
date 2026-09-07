<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNivelAcademicoTable extends Migration
{
    public function up()
    {
        Schema::create('nivel_academico', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->boolean('defecto')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nivel_academico');
    }
}
