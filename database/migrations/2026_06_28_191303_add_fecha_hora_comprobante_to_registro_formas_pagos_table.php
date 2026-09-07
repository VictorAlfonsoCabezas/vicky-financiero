<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaHoraComprobanteToRegistroFormasPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {

            $table->date('fecha_comprobante')
                  ->nullable()
                  ->after('numero_comprobante');

            $table->time('hora_comprobante')
                  ->nullable()
                  ->after('fecha_comprobante');

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

            $table->dropColumn([
                'fecha_comprobante',
                'hora_comprobante'
            ]);

        });
    }
}