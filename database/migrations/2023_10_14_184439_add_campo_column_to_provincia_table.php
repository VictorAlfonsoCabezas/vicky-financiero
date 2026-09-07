<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoColumnToProvinciaTable extends Migration
{
    public function up()
    {
        Schema::table('provincia', function (Blueprint $table) {
            $table->string('defecto')->after('nombre')->default(0);
        });
    }

    public function down()
    {
        Schema::table('provincia', function (Blueprint $table) {
            $table->dropColumn('defecto');
        });
    }
}
