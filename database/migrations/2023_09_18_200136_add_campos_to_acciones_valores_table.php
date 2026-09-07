<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToAccionesValoresTable extends Migration
{
    public function up()
    {
        Schema::table('acciones_valores', function (Blueprint $table) {
            $table->string('tipo', 20)->default(null)->nullable()->after('descripcion');
            $table->boolean('bloqueado')->default(true)->after('tipo');
        });
    }

    public function down()
    {
        Schema::table('acciones_valores', function (Blueprint $table) {
            $table->dropColumn('tipo');
            $table->dropColumn('bloqueado');
        });
    }
}
