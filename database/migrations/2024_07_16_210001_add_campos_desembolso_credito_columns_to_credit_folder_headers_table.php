<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposDesembolsoCreditoColumnsToCreditFolderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->integer('forma_pago_id')->after('status')->nullable();
            $table->string('documento_desembolso')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->dropColumn('forma_pago_id');
            $table->dropColumn('documento_desembolso');
        });
    }
}
