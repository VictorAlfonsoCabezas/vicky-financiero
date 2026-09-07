<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoColumnToTipoDocumentoTable extends Migration
{
    public function up()
    {
        Schema::table('tipo_documento', function (Blueprint $table) {
            $table->boolean('defecto')->after('nombre')->default(false);
        });
    }

    public function down()
    {
        Schema::table('tipo_documento', function (Blueprint $table) {
            $table->dropColumn('defecto');
        });
    }
}
