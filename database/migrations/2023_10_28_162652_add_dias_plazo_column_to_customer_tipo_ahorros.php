<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiasPlazoColumnToCustomerTipoAhorros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->integer('dias_plazo')->after('pago')->nullable();
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
            $table->dropColumn('dias_plazo');
        });
    }
}
