<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoAhorroIdToIntCalculoDetail extends Migration
{
    public function up()
    {
        Schema::table('int_calculo_detail', function (Blueprint $table) {
            $table->bigInteger('tipo_ahorro_id')->unsigned()->after('customer_id');
        });
    }

    public function down()
    {
        Schema::table('int_calculo_detail', function (Blueprint $table) {
            $table->dropColumn('tipo_ahorro_id');
        });
    }
}
