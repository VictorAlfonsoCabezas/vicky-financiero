<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartolaDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cartola_details', function (Blueprint $table) {
            $table->id();
            $table->string('cartola_header_code');
            $table->string('type_transaction_id')->nullable();
            $table->string('type_transaction_name')->nullable();
            $table->string('type_transaction_action')->nullable();
            $table->decimal('valor_transaction', 8, 2)->default(0);
            $table->date('date_transaction')->nullable();
            $table->decimal('saldo_transaction', 8, 2)->default(0);
            $table->integer('interes_id')->nullable();
            $table->decimal('interes_valor', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('cartola_details');
    }

}
