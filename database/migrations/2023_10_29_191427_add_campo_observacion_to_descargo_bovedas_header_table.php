<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoObservacionToDescargoBovedasHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->string('observacion')->nullable()->after('valor');
        });
    }

    public function down()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
}
