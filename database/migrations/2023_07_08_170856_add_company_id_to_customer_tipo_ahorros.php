<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdToCustomerTipoAhorros extends Migration
{
    public function up()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->bigInteger('company_id')->unsigned()->after('id');
        });
    }

    public function down()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
