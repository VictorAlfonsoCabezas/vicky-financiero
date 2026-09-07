<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservacionToRegistroFormasPagosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            // text() es el tipo ideal para ingresar texto, notas o descripciones largas
            $table->text('observacion')->nullable()->after('numero_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
}