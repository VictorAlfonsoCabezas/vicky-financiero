<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsientosHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('asientos_header', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->boolean('manual')->default(false);
            $table->datetime('fecha_creacion');
            $table->integer('user_created');
            $table->string('descripcion')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asientos_header');
    }
}
