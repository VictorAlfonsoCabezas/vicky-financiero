<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeCronColumnToCompanyTable extends Migration
{
    
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->time('time_cron')->after('numero_decimales')->nullable()->default(null);
        });
    }

    
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('time_cron');
        });
    }
}
