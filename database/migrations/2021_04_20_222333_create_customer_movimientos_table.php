<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerMovimientosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('customer_movimientos', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('customer_code');
            $table->string('afecta',2)->default('C');
            $table->string('customer_name')->nullable();
            $table->string('customer_ruc')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_telefono')->nullable();
            $table->bigInteger('type_transaction_id')->unsigned();
            $table->foreign('type_transaction_id')->references('id')->on('type_transactions');
            $table->string('type_transaction_name')->nullable();
            $table->string('type_transaction_action')->nullable();
            $table->decimal('valor_movimiento', 8, 2)->default(0);
            $table->decimal('saldo_general', 8, 2)->default(0);
            $table->string('observation')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_cancel_id')->nullable();
            $table->string('user_cancel_name')->nullable();
            $table->date('date_cancel')->nullable();
            $table->time('hour_cancel')->nullable();
            $table->string('razon_cancel')->nullable();
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
        Schema::dropIfExists('customer_movimientos');
    }

}
