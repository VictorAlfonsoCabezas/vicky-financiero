<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCustomer extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->integer('user_id')->after('code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
