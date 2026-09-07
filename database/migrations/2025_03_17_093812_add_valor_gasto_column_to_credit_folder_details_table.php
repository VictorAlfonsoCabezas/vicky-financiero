<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValorGastoColumnToCreditFolderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_details', function (Blueprint $table) {
            $table->decimal('valor_gasto', 8,2)->default('0.00')->after('notificado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_folder_details', function (Blueprint $table) {
            $table->dropColumn('valor_gasto');
        });
    }
}
