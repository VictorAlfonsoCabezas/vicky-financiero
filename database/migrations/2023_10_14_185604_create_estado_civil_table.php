<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoCivilTable extends Migration
{
    public function up()
    {
        Schema::create('estado_civil', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre', 60);
            $table->boolean('defecto')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estado_civil');
    }
}
