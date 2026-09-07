<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPorcentajeValorAdministrativoColumnsToCreditFolderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->string('administrativo_porcentaje_valor')->default('VALOR')->after('gasto_administrativo');
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
            $table->dropColumn('administrativo_porcentaje_valor');
        });
    }
}
