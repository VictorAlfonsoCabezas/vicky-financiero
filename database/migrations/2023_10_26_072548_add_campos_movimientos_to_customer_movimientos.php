<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposMovimientosToCustomerMovimientos extends Migration
{
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->integer('customer_id')->after('company_id')->nullable();
            $table->integer('tipo_ahorro_detalle_id')->after('customer_tipo_ahorro_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('tipo_ahorro_detalle_id');
        });
    }
}
