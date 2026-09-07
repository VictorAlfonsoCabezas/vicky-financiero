<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposParametrizacionPrestamosColumnsToCreditFolderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->decimal('gasto_administrativo', 8, 2)->default(0.00)->after('valor_liquidar');
            $table->boolean('encaje')->default(false)->after('gasto_administrativo');
            $table->string('encaje_credito_cuenta')->nullable()->after('encaje');
            $table->string('encaje_porcentaje_valor')->nullable()->after('encaje_credito_cuenta');
            $table->decimal('encaje_cantidad', 8, 2)->default(0.00)->after('encaje_porcentaje_valor');
            $table->decimal('encaje_valor', 8, 2)->default(0.00)->after('encaje_porcentaje_valor');
            $table->decimal('primer_gasto', 8, 2)->default(0.00)->after('encaje_porcentaje_valor');
            $table->decimal('segundo_gasto', 8, 2)->default(0.00)->after('encaje_porcentaje_valor');
            $table->decimal('tercer_gasto', 8, 2)->default(0.00)->after('encaje_porcentaje_valor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->dropColumn('gasto_administrativo');
            $table->dropColumn('encaje');
            $table->dropColumn('encaje_credito_cuenta');
            $table->dropColumn('encaje_porcentaje_valor');
            $table->dropColumn('encaje_cantidad');
            $table->dropColumn('encaje_valor');
            $table->dropColumn('primer_gasto');
            $table->dropColumn('segundo_gasto');
            $table->dropColumn('tercer_gasto');
        });
    }
}
