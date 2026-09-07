<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentezcoTable extends Migration
{
   
    public function up()
    {
        Schema::create('parentezco', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',100)->nullable();
            $table->string('description',255)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
     
    public function down()
    {
        Schema::dropIfExists('parentezco');
    }
}

