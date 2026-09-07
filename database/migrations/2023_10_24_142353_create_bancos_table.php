<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancosTable extends Migration
{
    public function up()
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('tipo_cuenta_id');
            $table->datetime('fecha_creacion'); 
            $table->string('nombre', 50);
            $table->string('descripcion', 255);
            $table->string('numero_cuenta', 15);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('bancos');
    }
}
