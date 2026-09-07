<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormasPagoCollumnToCustomerMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->string('forma_pago_name')->after('type_transaction_action')->nullable();
            $table->integer('forma_pago_id')->after('type_transaction_action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('forma_pago_name');
            $table->dropColumn('forma_pago_id');
        });
    }
}
