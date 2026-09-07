<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposParametrizablesGastosCreditosColumnsToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->string('nombre_primer_gasto_credito')->default('Gasto 1')->after('fecha_inicio_contable');
            $table->string('nombre_segundo_gasto_credito')->default('Gasto 2')->after('nombre_primer_gasto_credito');
            $table->string('nombre_tercer_gasto_credito')->default('Gasto 3')->after('nombre_segundo_gasto_credito');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('nombre_primer_gasto_credito');
            $table->dropColumn('nombre_segundo_gasto_credito');
            $table->dropColumn('nombre_tercer_gasto_credito');
        });
    }
}
