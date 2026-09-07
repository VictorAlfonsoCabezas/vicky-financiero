<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodigoToImpuestosTable extends Migration
{
    public function up()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->after('valor');
        });
    }

    public function down()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
}
