<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormaPagoFieldsToCustomerTipoAhorrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table -> unsignedBigInteger('forma_pago_id') -> nullable() -> after('pago');
            $table -> string('comprobante') -> nullable() -> after('forma_pago_id');
            $table -> unsignedBigInteger('banco_id') -> nullable() -> after('comprobante');
            $table -> string('numero_deposito') -> nullable() -> after('banco_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn([
                'forma_pago_id',
                'comprobante',
                'banco_id',
                'numero_deposito'
            ]);
        });
    }
}
