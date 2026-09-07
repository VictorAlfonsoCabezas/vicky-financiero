<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoColumnToCajasTable extends Migration
{
    public function up()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('customer_movimiento_id')->after('descuadre')->nullable();
            $table->string('observacion_cierre')->after('customer_movimiento_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('customer_movimiento_id');
            $table->dropColumn('observacion_cierre');
        });
    }
}
