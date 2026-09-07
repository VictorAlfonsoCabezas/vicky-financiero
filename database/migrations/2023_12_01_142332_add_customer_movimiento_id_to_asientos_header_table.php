<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerMovimientoIdToAsientosHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->datetime('fecha_contable')->after('manual')->nullable();
            $table->integer('customer_movimiento_id')->after('descripcion')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropColumn('fecha_contable');
            $table->dropColumn('customer_movimiento_id');
        });
    }
}
