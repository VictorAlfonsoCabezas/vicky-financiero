<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoConceptoTable extends Migration
{
    public function up()
    {
        Schema::create('tipo_concepto', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre', 60);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_concepto');
    }
}
