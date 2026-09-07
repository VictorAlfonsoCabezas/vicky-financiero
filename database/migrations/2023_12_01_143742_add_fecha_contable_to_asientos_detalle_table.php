<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaContableToAsientosDetalleTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->datetime('fecha_contable')->after('debe_haber')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asientos_detalle', function (Blueprint $table) {
            $table->dropColumn('fecha_contable');
        });
    }
}
