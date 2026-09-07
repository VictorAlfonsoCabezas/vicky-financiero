<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPorcentajeInteresColumnToCreditFolderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->decimal('porcentaje_interes_prestamo', 8,2)->default('0.00')->after('valor_solicitado');
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
            $table->dropColumn('porcentaje_interes_prestamo');
        });
    }
}
