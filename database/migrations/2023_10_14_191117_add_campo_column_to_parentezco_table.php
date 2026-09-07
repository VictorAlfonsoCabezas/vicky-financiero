<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoColumnToParentezcoTable extends Migration
{
    public function up()
    {
        Schema::table('parentezco', function (Blueprint $table) {
            $table->string('defecto')->after('nombre')->default(false);
        });
    }

    public function down()
    {
        Schema::table('parentezco', function (Blueprint $table) {
            $table->dropColumn('defecto');
        });
    }
}
