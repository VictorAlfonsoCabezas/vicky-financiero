<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoNewColumnToCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->integer('tipo_documento_id')->after('country_id')->nullable();
            $table->integer('genero_id')->after('tipo_documento_id')->nullable();
            $table->integer('estado_civil_id')->after('genero_id')->nullable();
            $table->integer('provincia_id')->after('estado_civil_id')->nullable();
            $table->integer('ciudad_id')->after('provincia_id')->nullable();
            $table->integer('parroquia_id')->after('ciudad_id')->nullable();
            $table->integer('parentezco_id')->after('parroquia_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('tipo_documento_id');
            $table->dropColumn('genero_id');
            $table->dropColumn('estado_civil_id');
            $table->dropColumn('provincia_id');
            $table->dropColumn('ciudad_id');
            $table->dropColumn('parroquia_id');
            $table->dropColumn('parentezco_id');
        });
    }
}
