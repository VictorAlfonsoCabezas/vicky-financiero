<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatosBancoToCustomerMovimientosTable extends Migration
{
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->integer('banco_id')->after('type_transaction_action')->nullable();
            $table->string('numero_deposito', 60)->after('banco_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('banco_id');
            $table->dropColumn('numero_deposito');
        });
    }
}
