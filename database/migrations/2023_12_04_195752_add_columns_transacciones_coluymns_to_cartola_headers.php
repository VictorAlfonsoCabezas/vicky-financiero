<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTransaccionesColuymnsToCartolaHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cartola_headers', function (Blueprint $table) {
            $table->string('numero')->after('code')->nullable();
            $table->date('date_update')->after('customer_date_create')->nullable();
            $table->time('hour_update')->after('customer_date_create')->nullable();
            $table->integer('user_update')->after('customer_date_create')->nullable();
            
            $table->date('date_create')->after('customer_date_create')->nullable();
            $table->time('hour_create')->after('customer_date_create')->nullable();
            $table->integer('user_create')->after('customer_date_create')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cartola_headers', function (Blueprint $table) {
            $table->dropColumn('numero');
            $table->dropColumn('date_create');
            $table->dropColumn('hour_create');
            $table->dropColumn('user_create');
            $table->dropColumn('date_update');
            $table->dropColumn('hour_update');
            $table->dropColumn('user_update');
        });
    }
}
