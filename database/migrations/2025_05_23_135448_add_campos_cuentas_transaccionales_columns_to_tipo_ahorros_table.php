<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposCuentasTransaccionalesColumnsToTipoAhorrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
             $table->boolean('cuenta_transaccional')->default(false)->after('class');
             $table->boolean('administrativo_procentaje')->default(false)->after('cuenta_transaccional');
             $table->decimal('administrativo_valor', 8,2)->default('0.00')->after('administrativo_procentaje');
             $table->integer('administrativo_fecha')->nullable()->after('administrativo_valor');
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
            $table->dropColumn('cuenta_transaccional');
            $table->dropColumn('administrativo_procentaje');
            $table->dropColumn('administrativo_valor');
            $table->dropColumn('administrativo_fecha');
        });
    }
}
