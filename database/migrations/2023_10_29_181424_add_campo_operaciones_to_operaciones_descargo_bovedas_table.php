<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoOperacionesToOperacionesDescargoBovedasTable extends Migration
{
    public function up()
    {
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {
            $table->string('afecta', 20)->after('nombre_corto')->nullable()->default('E');
            $table->string('accion', 20)->after('afecta')->nullable()->default('S');
        });
    }

    public function down()
    {
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {
            $table->dropColumn('afecta');
            $table->dropColumn('accion');
        });
    }
}
