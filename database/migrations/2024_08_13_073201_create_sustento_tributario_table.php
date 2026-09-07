<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSustentoTributarioTable extends Migration
{
    public function up()
    {
        Schema::create('sustento_tributario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_tributario', 10);
            $table->string('nombre', 200);
            $table->boolean('credito_tributario')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sustento_tributario');
    }
}
