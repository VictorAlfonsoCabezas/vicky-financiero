<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaPagoAutomaticoToUtilidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utilidads', function (Blueprint $table) {
           $table->date('fecha_pago_automatico')->nullable()->after('pago_automatico'); 
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
            $table->dropColumn('fecha_pago_automatico');
        });
    }
}
