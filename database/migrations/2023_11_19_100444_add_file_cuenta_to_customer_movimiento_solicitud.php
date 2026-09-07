<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileCuentaToCustomerMovimientoSolicitud extends Migration
{
    public function up()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->integer('customer_tipo_ahorro_id')->after('fecha_creacion')->nullable()->default(null);
            $table->string('path')->after('user_rechazado')->nullable();
            $table->string('archivo')->after('path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->dropColumn('customer_tipo_ahorro_id');
            $table->dropColumn('path');
            $table->dropColumn('archivo');
        });
    }
}
