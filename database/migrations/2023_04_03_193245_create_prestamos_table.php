<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestamosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('name')->nullable();
            $table->decimal('interes')->default('0.00');
            $table->decimal('fondo_desgravamen')->default('0.00');
            $table->decimal('valor_maximo')->default('0.00');
            $table->decimal('valor_minimo')->default('0.00');
            $table->string('tipo', 5)->default('F');
            $table->string('status', 5)->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('prestamos');
    }

}
