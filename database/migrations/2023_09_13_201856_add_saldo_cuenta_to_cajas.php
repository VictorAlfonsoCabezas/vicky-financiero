<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaldoCuentaToCajas extends Migration
{
    public function up()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('saldo_cuenta')->default('0.00')->after('code');
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('saldo_cuenta');
        });
    }
}
