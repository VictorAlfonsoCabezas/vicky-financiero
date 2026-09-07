<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBovedasTable extends Migration
{
    public function up()
    {
        Schema::create('bovedas', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->datetime('fecha_creacion');
            $table->string('nombre');
            $table->string('descripcion', 255);
            $table->boolean('principal')->default(false);
            $table->boolean('boveda')->default(false);
            $table->boolean('caja')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bovedas');
    }
}
