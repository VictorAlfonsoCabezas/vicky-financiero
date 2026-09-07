<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTables extends Migration
{
    public function up()
    {
        DB::table('country')->truncate();
        DB::table('ciudad')->truncate();
        DB::table('provincia')->truncate();
        DB::table('parroquia')->truncate();
    }

    public function down()
    {
        //
    }
}
