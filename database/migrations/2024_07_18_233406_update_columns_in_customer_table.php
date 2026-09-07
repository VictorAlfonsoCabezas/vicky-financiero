<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsInCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('tiempo_vivienda', 30)->change();
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            // $table->string('tiempo_vivienda')->change();
        });
    }
}
