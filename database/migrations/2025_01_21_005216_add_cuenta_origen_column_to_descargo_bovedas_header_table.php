<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCuentaOrigenColumnToDescargoBovedasHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->integer('cuenta_origen')->nullable()->after('customer_movimiento_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->dropColumn('cuenta_origen');
        });
    }
}
