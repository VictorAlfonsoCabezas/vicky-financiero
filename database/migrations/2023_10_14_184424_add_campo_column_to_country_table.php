<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoColumnToCountryTable extends Migration
{
    public function up()
    {
        Schema::table('country', function (Blueprint $table) {
            $table->string('defecto')->after('nombre')->default(0);
        });
    }

    public function down()
    {
        Schema::table('country', function (Blueprint $table) {
            $table->dropColumn('defecto');
        });
    }
}
