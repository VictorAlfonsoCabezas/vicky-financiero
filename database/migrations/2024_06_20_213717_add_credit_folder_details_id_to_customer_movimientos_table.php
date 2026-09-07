<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditFolderDetailsIdToCustomerMovimientosTable extends Migration
{
    public function up()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->integer('credit_folder_details_id')->after('saldo_general')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->dropColumn('credit_folder_details_id');
        });
    }
}
