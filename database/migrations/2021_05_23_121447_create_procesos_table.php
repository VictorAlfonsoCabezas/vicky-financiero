<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesosTable extends Migration {

    public function up() {
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('description')->nullable();
            $table->string('recurrencia', 3)->nullable();
            $table->string('valor', 20)->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->string('dia_update', 2)->nullable();
            $table->string('mes_update')->nullable();
            $table->string('anio_update')->nullable();
            $table->date('date_update')->nullable();
            $table->string('status')->default('SINCORRER');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('procesos');
    }

}
