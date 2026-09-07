<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerMovimientoSolicitudIdColmunToDescargoBovedasHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->string('customer_movimiento_solicitud_id')->after('cajas_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->dropColumn('customer_movimiento_solicitud_id');
        });
    }
}
