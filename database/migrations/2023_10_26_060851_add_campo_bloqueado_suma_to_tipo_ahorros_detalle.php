<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoBloqueadoSumaToTipoAhorrosDetalle extends Migration
{
    public function up()
    {
        Schema::table('tipo_ahorros_detalle', function (Blueprint $table) {
            $table->boolean('bloqueado')->after('siglas')->default(false);
            $table->boolean('suma')->after('bloqueado')->default(false);
        });
    }

    public function down()
    {
        Schema::table('tipo_ahorros_detalle', function (Blueprint $table) {
            $table->dropColumn('bloqueado');
            $table->dropColumn('suma');
        });
    }
}
