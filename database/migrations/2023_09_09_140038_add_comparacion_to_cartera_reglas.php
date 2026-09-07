<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComparacionToCarteraReglas extends Migration
{
    public function up()
    {
        Schema::table('cartera_reglas', function (Blueprint $table) {
            $table->string('comparacion', 10)->default('=')->after('mensaje');
        });
    }

    public function down()
    {
        Schema::table('cartera_reglas', function (Blueprint $table) {
            $table->dropColumn('comparacion');
        });
    }
}
