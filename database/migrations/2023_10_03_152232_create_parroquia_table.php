<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParroquiaTable extends Migration
{
    
    public function up()
    {
        Schema::create('parroquia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('provincia_id');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parroquia');
    }
}
