<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposBancoToCustomerMovimientoSolicitudTable extends Migration
{
    public function up()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->integer('banco_id')->nullable()->after('customer_id');
            $table->string('numero_deposito', 60)->nullable()->after('banco_id');
            $table->integer('type_transaction_id')->nullable()->after('numero_deposito');
            $table->integer('forma_pago_id')->nullable()->after('type_transaction_id');
            $table->integer('customer_movimiento_id')->nullable()->after('forma_pago_id');
        });
    }

    public function down()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->dropColumn('banco_id');
            $table->dropColumn('numero_deposito');
            $table->dropColumn('type_transaction_id');
            $table->dropColumn('forma_pago_id');
            $table->dropColumn('customer_movimiento_id');
        });
    }
}
