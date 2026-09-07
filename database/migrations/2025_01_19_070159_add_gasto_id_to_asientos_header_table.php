<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGastoIdToAsientosHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->integer('gasto_id')->nullable()->after('customer_movimiento_id')->comment('tiene su tabla propia gastos');
        });
    }

    public function down()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropColumn('gasto_id');
        });
    }
}
