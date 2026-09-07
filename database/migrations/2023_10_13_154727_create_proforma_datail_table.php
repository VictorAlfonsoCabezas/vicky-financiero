<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaDatailTable extends Migration
{
    
    public function up()
    {
        Schema::create('proforma_datail', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->datetime('fecha_creacion');
            $table->integer('producto_id');
            $table->decimal('cantidad');
            $table->decimal('precio')->default(0.00);
            $table->decimal('subtotal')->default(0.00);
            $table->string('descripcion', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proforma_datail');
    }
}
