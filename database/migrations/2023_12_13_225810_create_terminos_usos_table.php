<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminosUsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminos_usos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->date('date_create')->nullable();
            $table->time('hour_create')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terminos_usos');
    }
}
