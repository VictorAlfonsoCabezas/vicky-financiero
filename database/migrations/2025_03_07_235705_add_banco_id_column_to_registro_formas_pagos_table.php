<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBancoIdColumnToRegistroFormasPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            $table->integer('banco_id')->nullable()->after('forma_pago_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            $table->dropColumn('banco_id');
        });
    }
}
