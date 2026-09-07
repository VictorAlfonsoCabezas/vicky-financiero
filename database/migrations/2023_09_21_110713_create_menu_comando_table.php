<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuComandoTable extends Migration
{
    
    public function up()
    {
        Schema::create('menu_comando', function (Blueprint $table) {
            $table->id();
            $table->integer('menu_id');
            $table->integer('comando_id');
            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('menu_comando');
    }
}
