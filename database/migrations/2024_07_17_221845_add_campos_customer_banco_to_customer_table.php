<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposCustomerBancoToCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->integer('banco_id')->nullable()->after('longitud');
            $table->integer('tipo_cuenta_id')->nullable()->after('banco_id');
            $table->string('no_cuenta', 60)->nullable()->after('tipo_cuenta_id');
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('banco_id');
            $table->dropColumn('tipo_cuenta_id');
            $table->dropColumn('no_cuenta');
        });
    }
}
