<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassToTipoAhorros extends Migration
{
    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->string('class', 20)->after('rango_tiempo')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('class');
        });
    }
}
