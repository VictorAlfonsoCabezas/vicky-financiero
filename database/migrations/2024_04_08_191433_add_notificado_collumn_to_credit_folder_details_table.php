<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificadoCollumnToCreditFolderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_details', function (Blueprint $table) {
            $table->integer('notificado')->after('hour_cancel')->default(0);
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
            $table->dropColumn('notificado');
        });
    }
}
