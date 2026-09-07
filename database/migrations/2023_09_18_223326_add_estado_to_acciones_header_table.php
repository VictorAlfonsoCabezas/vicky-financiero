<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToAccionesHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('acciones_header', function (Blueprint $table) {
            $table->string('estado', 20)->default('ACTIVO')->after('class');
        });
    }

    public function down()
    {
        Schema::table('acciones_header', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
