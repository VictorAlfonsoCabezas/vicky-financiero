<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeneficiarioProgramadoColumnToHeaderCalculadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('header_calculadoras', function (Blueprint $table) {
            $table->string('beneficiarioProgramado')->nullable()->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('header_calculadoras', function (Blueprint $table) {
            $table->dropColumn('beneficiarioProgramado');
        });
    }
}
