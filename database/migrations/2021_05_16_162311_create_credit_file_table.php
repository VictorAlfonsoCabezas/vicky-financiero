<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditFileTable extends Migration {

    public function up() {
        Schema::create('credit_file', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->bigInteger('credit_header_id')->unsigned();
            $table->foreign('credit_header_id')->references('id')->on('credit_folder_headers');
            $table->date('date_created');
            $table->time('hour_created');
            $table->string('descripcion', 255)->nullable();
            $table->string('archivo', 60);
            $table->string('path', 255)->nullable();
            $table->string('formato', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('credit_file');
    }

}
