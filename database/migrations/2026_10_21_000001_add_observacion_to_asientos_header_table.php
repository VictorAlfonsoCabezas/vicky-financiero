<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservacionToAsientosHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->text('observacion')->nullable()->after('descripcion');
        });
    }

    public function down()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }
}
