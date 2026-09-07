<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoColumnToCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->integer('country_id')->default(1)->after('customer_tarifa_interes');
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
    }
}
