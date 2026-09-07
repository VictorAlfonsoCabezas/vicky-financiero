<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartolaHeadersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cartola_headers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->integer('customer_id')->nullable();
            $table->string('customer_code')->nullable();
            $table->string('customer_name')->nullable();
            $table->date('ultimo_interes')->nullable();
            $table->date('customer_date_create')->nullable();
            $table->string('status', 15)->default('ESPACIOS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cartola_headers');
    }

}
