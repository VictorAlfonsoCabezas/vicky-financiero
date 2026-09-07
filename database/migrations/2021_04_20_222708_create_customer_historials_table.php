<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerHistorialsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('customer_historials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('customer_code');
            $table->string('customer_movimiento_code');
            $table->string('afecta',2)->default('C');
            $table->decimal('valor_movimiento', 8, 2)->default(0);
            $table->decimal('saldo_general', 8, 2)->default(0);
            $table->integer('type_transaction_id');
            $table->string('type_transaction_name',60)->nullable();
            $table->string('type_transaction_action')->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('customer_historials');
    }

}
