<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroComprobanteToRegistroFormasPagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            $table -> string('numero_comprobante')
                ->nullable()
                ->after('valor');
                //puedes
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
            $table -> dropColumn('numero_comprobante');
        });
    }
}
