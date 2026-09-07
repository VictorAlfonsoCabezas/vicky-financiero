<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerFoldersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('customer_folders', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('customer_code');
            $table->string('customer_name')->nullable();
            $table->string('customer_ruc')->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->string('status')->default('PENDIENTE');
            $table->timestamps();
        });
    }

    
    public function down() {
        Schema::dropIfExists('customer_folders');
    }

}
