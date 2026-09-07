<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosCategoriasTable extends Migration
{
    public function up()
    {
        Schema::create('gastos_categorias', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('nombre')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos_categorias');
    }
}
