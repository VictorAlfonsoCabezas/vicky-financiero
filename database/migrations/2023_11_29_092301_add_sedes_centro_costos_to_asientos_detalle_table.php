<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSedesCentroCostosToAsientosDetalleTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->integer('sedes_centro_costos_id')->after('valor')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->dropColumn('sedes_centro_costos_id');
        });
    }
}
