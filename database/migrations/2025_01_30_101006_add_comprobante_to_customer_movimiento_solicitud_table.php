<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComprobanteToCustomerMovimientoSolicitudTable extends Migration
{
    public function up()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->string('comprobante', 60)->nullable()->after('banco_id');
        });
    }

    public function down()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->dropColumn('comprobante');
        });
    }
}
