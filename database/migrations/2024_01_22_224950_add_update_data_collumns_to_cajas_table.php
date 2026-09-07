<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdateDataCollumnsToCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('user_update_id')->after('status')->nullable();
            $table->string('user_update_name')->after('status')->nullable();
            $table->date('date_update')->after('status')->nullable();
            $table->time('hour_update')->after('status')->nullable();
            $table->string('descripcion_update')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('user_update_id');
            $table->dropColumn('user_update_name');
            $table->dropColumn('date_update');
            $table->dropColumn('hour_update');
            $table->dropColumn('descripcion_update');
        });
    }
}
