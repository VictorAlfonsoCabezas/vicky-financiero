<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgramadoToTipoAhorros extends Migration
{
    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->boolean('programado')->after('company_id')->default(false);
        });
    }

    public function down()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('programado');
        });
    }
}
