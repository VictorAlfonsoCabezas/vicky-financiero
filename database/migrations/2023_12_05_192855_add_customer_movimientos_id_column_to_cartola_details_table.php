<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerMovimientosIdColumnToCartolaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cartola_details', function (Blueprint $table) {
            $table->integer('customer_movimientos_id')->after('cartola_headers_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cartola_details', function (Blueprint $table) {
            $table->dropColumn('customer_movimientos_id');
        });
    }
}
