<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConceptoToAsientosHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->integer('concepto_id')->after('user_created')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropColumn('concepto_id');
        });
    }
}
