<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInteresValorColumnToInteresFijoParametrizadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interes_fijo_parametrizado', function (Blueprint $table) {
            $table->boolean('porcentaje')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interes_fijo_parametrizado', function (Blueprint $table) {
            $table->dropColumn('porcentaje');
        });
    }
}
