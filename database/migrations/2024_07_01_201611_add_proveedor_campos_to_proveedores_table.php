<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProveedorCamposToProveedoresTable extends Migration
{
    public function up()
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->integer('plan_cuenta_id')->after('email')->nullable();
            $table->integer('centro_costos_id')->after('plan_cuenta_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn('plan_cuenta_id');
            $table->dropColumn('centro_costos_id');
        });
    }
}
