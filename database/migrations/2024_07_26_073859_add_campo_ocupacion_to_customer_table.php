<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoOcupacionToCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('ocupacion', 60)->nullable()->default('NINGUNO')->after('conyuge_cargo_empresa')->comment('NINGUNO, EMPRESA, NEGOCIO');
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('ocupacion');
        });
    }
}
