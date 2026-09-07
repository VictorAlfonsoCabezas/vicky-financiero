<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSumaValoresPrestamoColumnToCreditFolderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->boolean('suma_valores_gastos_prestamo')->default(0)->after('valor_solicitado');
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
            $table->dropColumn('suma_valores_gastos_prestamo');
        });
    }
}
