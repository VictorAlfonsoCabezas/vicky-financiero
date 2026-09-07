<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFondoHeadersTable extends Migration {

    public function up() {
        Schema::create('fondo_headers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->decimal('valor_inicial', 8, 2)->default(0);
            $table->decimal('valor_ingreso', 8, 2)->default(0);
            $table->decimal('valor_egreso', 8, 2)->default(0);
            $table->decimal('valor_total', 8, 2)->default(0);
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('fondo_headers');
    }

}
