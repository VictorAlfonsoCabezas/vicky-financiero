<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposImpuestoToImpuestosTable extends Migration
{
    public function up()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            $table->boolean('por_defecto')->default(false)->after('valor');
        });
    }

    public function down()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            $table->dropColumn('por_defecto');
        });
    }
}
