<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccionesValoresTable extends Migration
{
    public function up()
    {
        Schema::create('acciones_valores', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre',60);
            $table->datetime('fecha_creacion');
            $table->string('descripcion', 255);
            $table->string('operacion',5); 
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acciones_valores');
    }
}
