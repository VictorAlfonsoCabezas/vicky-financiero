<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoRetencionTable extends Migration
{
    public function up()
    {
        Schema::create('tipo_retencion', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_retencion');
    }
}
