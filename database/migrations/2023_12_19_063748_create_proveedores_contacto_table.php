<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresContactoTable extends Migration
{
    public function up()
    {
        Schema::create('proveedores_contacto', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('proveedor_id')->nullable();
            $table->string('nombre_contacto', 60)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 60)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proveedores_contacto');
    }
}
