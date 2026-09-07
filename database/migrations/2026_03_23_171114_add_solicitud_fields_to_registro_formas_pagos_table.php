<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSolicitudFieldsToRegistroFormasPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            $table->boolean('solicitado')->default(false)->after('status');

            $table->string('usuario_solicitud')->nullable()->after('solicitado');
            $table->unsignedBigInteger('usuario_id_solicitud')->nullable()->after('usuario_solicitud');

            $table->date('fecha_solicitud')->nullable()->after('usuario_id_solicitud');
            $table->time('hora_solicitud')->nullable()->after('fecha_solicitud');
        });
    }

    public function down(): void
    {
        Schema::table('registro_formas_pagos', function (Blueprint $table) {
            $table->dropColumn([
                'solicitado',
                'usuario_solicitud',
                'usuario_id_solicitud',
                'fecha_solicitud',
                'hora_solicitud'
            ]);
        });
    }
}
