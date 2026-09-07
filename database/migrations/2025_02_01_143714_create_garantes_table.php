<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garantes', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('credit_folder_headers_id');
            $table->integer('customer_id');
            $table->string('customer_name');
            $table->string('customer_identificacion');
            $table->string('customer_conyuge_name')->nullable();
            $table->string('customer_conyuge_identificacion')->nullable();
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
    public function down()
    {
        Schema::dropIfExists('garantes');
    }
}
