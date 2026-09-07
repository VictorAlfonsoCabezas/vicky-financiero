<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditoDescargoBovedaColumnToFormasPagoTable extends Migration
{
    
    public function up()
    {
        Schema::table('formas_pago', function (Blueprint $table) {
            $table->boolean('credito_descargo_boveda')->after('code')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formas_pago', function (Blueprint $table) {
            $table->dropColumn('gastos_id');
        });
    }
}
