<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperacionesDescargoBovedasTable extends Migration
{
    public function up()
    {
        Schema::create('operaciones_descargo_bovedas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('nombre');
            $table->integer('descripcion');
            $table->integer('nombre_corto');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('operaciones_descargo_bovedas');
    }
}
