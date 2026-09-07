<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAhorroPrestamoColumnToTipoAhorrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->boolean('ahorro_prestamo')->default(0)->after('cuenta_certificado_valor_max');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('ahorro_prestamo');
        });
    }
}
