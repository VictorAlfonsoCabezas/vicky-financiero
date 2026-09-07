<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBovedasTable extends Migration
{
    
    public function up()
    {
        Schema::create('user_bovedas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('user_id');
            $table->integer('bovedas_id');
            $table->timestamps();
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('user_bovedas');
    }
}
