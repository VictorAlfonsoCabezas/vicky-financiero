<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaHoraAprobacionToRegistroFormasLiquidacionTable extends Migration
{
    public function up()
    {
        Schema::table('registro_formas_liquidacion', function (Blueprint $table) {
            $table->date('fecha_aprobacion')->nullable()->after('hora_solicitud');
            $table->time('hora_aprobacion')->nullable()->after('fecha_aprobacion');
        });
    }

    public function down()
    {
        Schema::table('registro_formas_liquidacion', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_aprobacion',
                'hora_aprobacion'
            ]);
        });
    }
}