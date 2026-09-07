<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutomaticoIdToCustomerMovimientos extends Migration
{
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->boolean('automatico')->after('razon_cancel')->default(false);
        });
    }

    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('automatico');
        });
    }
}
