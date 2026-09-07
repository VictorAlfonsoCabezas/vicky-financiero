<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaHeaderTable extends Migration
{
    
    public function up()
    {
        Schema::create('proforma_header', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->integer('customer_id');
            $table->string('codigo')->default('ABIERTO');
            $table->datetime('fecha_creacion');
            $table->datetime('fecha_caducidad');
            $table->string('descripcion', 255);
            $table->decimal('subtotal');
            $table->decimal('iva')->default(0.00);
            $table->decimal('iva_0')->default(0.00);
            $table->decimal('total')->default(0.00);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proforma_header');
    }
}
