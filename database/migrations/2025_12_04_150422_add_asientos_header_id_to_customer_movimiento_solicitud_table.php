<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsientosHeaderIdToCustomerMovimientoSolicitudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->unsignedBigInteger('asientos_header_id')->nullable()->after('archivo');
            $table->foreign('asientos_header_id')->references('id')->on('asientos_header')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_movimiento_solicitud', function (Blueprint $table) {
            $table->dropForeign(['asientos_header_id']);
            $table->dropColumn('asientos_header_id');
        });
    }
}
