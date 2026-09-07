<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarteraDetailTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cartera_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cartera_header_id')->unsigned();
            $table->foreign('cartera_header_id')->references('id')->on('cartera_header');
            $table->bigInteger('recurrencia_cartera_id')->unsigned();
            $table->foreign('recurrencia_cartera_id')->references('id')->on('recurrencia_cartera');
            $table->decimal('valores')->default('0.00');
            $table->date('date_save')->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cartera_detail');
    }

}
