<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescargoBovedasDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('descargo_bovedas_detalle', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');  
            $table->string('boveda_header_id');
            $table->datetime('fecha_creacion');
            $table->decimal('valor');
            $table->string('operacion');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('descargo_bovedas_detalle');
    }
}
