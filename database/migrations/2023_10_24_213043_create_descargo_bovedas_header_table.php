<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescargoBovedasHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('descargo_bovedas_header', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');    
            $table->string('operaciones_descargo_bovedas_id'); 
            $table->string('boveda_origen_id');
            $table->string('boveda_destino_id');
            $table->datetime('fecha_creacion');
            $table->boolean('estado')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('descargo_bovedas_header');
    }
}
