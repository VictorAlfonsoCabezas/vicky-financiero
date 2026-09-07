<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDenominacionBilletesTable extends Migration
{
   
    public function up()
    {
        Schema::create('denominacion_billetes', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre', 60);
            $table->decimal('valor');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('denominacion_billetes');
    }
}
