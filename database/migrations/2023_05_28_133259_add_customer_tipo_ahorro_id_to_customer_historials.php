<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerTipoAhorroIdToCustomerHistorials extends Migration
{
    public function up()
    {
        Schema::table('customer_historials', function (Blueprint $table) {
            $table->integer('customer_tipo_ahorro_id')->after('saldo_general')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer_historials', function (Blueprint $table) {
            $table->dropColumn('customer_tipo_ahorro_id');
        });
    }
}
