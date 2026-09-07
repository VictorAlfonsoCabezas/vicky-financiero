<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToAccionesHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('acciones_header', function (Blueprint $table) {
            $table->string('anio', 5)->after('capital');
            $table->string('mes', 5)->after('anio');
        });
    }

    public function down()
    {
        Schema::table('acciones_header', function (Blueprint $table) {
            $table->dropColumn('anio');
            $table->dropColumn('mes');
        });
    }
}
