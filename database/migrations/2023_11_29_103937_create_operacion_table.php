<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperacionTable extends Migration
{
    public function up()
    {
        Schema::create('operacion', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('nombre', 60)->nullable();
            $table->string('descripcion')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('operacion');
    }
}
