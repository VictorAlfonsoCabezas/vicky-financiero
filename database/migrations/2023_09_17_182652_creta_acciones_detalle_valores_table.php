<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CretaAccionesDetalleValoresTable extends Migration
{
    public function up()
    {
        Schema::create('acciones_detalle_valores', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('acciones_header_id');
            $table->integer('acciones_detalle_id');
            $table->integer('acciones_valores_id');
            $table->decimal('valor', 8,2)->default(0.00);
            $table->datetime('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acciones_detalle_valores');
    }
}
