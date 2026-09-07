<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContabilidadToCompanyTable extends Migration
{
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->boolean('contabilidad')->after('active_cron')->default(false);
        });
    }

    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('contabilidad');
        });
    }
}
