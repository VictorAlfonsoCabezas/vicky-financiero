<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaposEncajeColumnToTipoAhorrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->boolean('cuenta_encaje')->default(false)->after('ahorro_prestamo');
            $table->decimal('porcentaje_encaje', 8,2)->default('0.00')->after('cuenta_encaje');
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
            $table->dropColumn('cuenta_encaje');
            $table->dropColumn('porcentaje_encaje');
        });
    }
}
