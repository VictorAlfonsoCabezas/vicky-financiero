<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesesTable extends Migration
{
    
    public function up()
    {
        Schema::create('meses', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(true);
            $table->string('codigo',4)->nullable();
            $table->string('mes',25)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meses');
    }
}
