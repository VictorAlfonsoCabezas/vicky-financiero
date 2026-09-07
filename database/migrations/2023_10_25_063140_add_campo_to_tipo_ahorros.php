<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoToTipoAhorros extends Migration
{

    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->boolean('descargo_creditos')->after('company_id')->default(false);
        });
    }


    public function down()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('descargo_creditos');
        });
    }
}
