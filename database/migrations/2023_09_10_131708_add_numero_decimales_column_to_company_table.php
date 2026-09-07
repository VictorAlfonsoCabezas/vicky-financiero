<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroDecimalesColumnToCompanyTable extends Migration
{
   
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->integer('numero_decimales')->default(2)->nullable()->after('numero_dias_interes');
        });
    }

   
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('numero_decimales');
        });
    }
}
