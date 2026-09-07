<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPorcentagesValoresGastosColumnToCreditFolderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->boolean('porcentaje_primer_gasto')->default(0)->after('primer_gasto');
            $table->boolean('porcentaje_segundo_gasto')->default(0)->after('segundo_gasto');
            $table->boolean('porcentaje_tercer_gasto')->default(0)->after('tercer_gasto');
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
            $table->dropColumn('porcentaje_primer_gasto');
            $table->dropColumn('porcentaje_segundo_gasto');
            $table->dropColumn('porcentaje_tercer_gasto');
        });
    }
}
