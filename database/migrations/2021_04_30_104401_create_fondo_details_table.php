<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFondoDetailsTable extends Migration {

    public function up() {
        Schema::create('fondo_details', function (Blueprint $table) {
            $table->id();
            $table->string('code_header_id');
            $table->string('type_transaction_id')->nullable();
            $table->string('type_transaction_name')->nullable();
            $table->string('type_transaction_action')->nullable();
            $table->decimal('valor', 8, 2)->default(0);
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->string('observation_created')->nullable();
            $table->integer('user_cancel_id')->nullable();
            $table->string('user_cancel_name')->nullable();
            $table->string('observation_cancel')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('fondo_details');
    }

}
