<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoAhorrosIdToUtilidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utilidads', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_ahorros_id')->nullable()->after('fecha_pago_automatico');
            $table->foreign('tipo_ahorros_id')->references('id')->on('tipo_ahorros')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('utilidads', function (Blueprint $table) {
            $table->dropForeign(['tipo_ahorros_id']);
            $table->dropColumn('tipo_ahorros_id');
        });
    }
}
