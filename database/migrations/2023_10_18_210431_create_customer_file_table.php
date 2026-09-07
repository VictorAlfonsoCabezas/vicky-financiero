<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerFileTable extends Migration
{
    public function up()
    {
        Schema::create('customer_file', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('customer_id');
            $table->integer('customer_tipo_ahorros_id')->nullable();
            $table->integer('credit_header_id')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('archivo', 60);
            $table->string('path', 255)->nullable();
            $table->string('formato', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_file');
    }
}
