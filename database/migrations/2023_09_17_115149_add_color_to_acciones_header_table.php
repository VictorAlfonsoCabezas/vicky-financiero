<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorToAccionesHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('acciones_header', function (Blueprint $table) {
            $table->string('class', 20)->after('mes');
        });
    }

    public function down()
    {
        Schema::table('acciones_header', function (Blueprint $table) {
            $table->dropColumn('class');
        });
    }
}
