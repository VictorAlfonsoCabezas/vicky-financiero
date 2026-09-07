<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMargenesToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->integer('margen_top')->after('color_texto')->default(0);
            $table->integer('margen_dow')->after('color_texto')->default(0);
            $table->integer('margen_right')->after('color_texto')->default(0);
            $table->integer('margen_left')->after('color_texto')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('margen_top');
            $table->dropColumn('margen_dow');
            $table->dropColumn('margen_right');
            $table->dropColumn('margen_left');
        });
    }
}
