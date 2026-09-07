<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsClasificacionFormaPagoToFormasPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('formas_pago', function (Blueprint $table) {
            $table->integer('clasificacion_forma_pagos_id')->after('company_id')->nullable();
            $table->string('code', 10)->after('descripcion')->nullable();
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
            $table->dropColumn('clasificacion_forma_pagos_id');
            $table->dropColumn('code');
        });
    }
}
