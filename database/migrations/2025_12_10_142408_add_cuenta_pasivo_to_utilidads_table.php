<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCuentaPasivoToUtilidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utilidads', function (Blueprint $table) {
            $table->unsignedBigInteger('cuenta_pasivo_id')->nullable()->after('tipo_ahorros_id');
            $table->foreign('cuenta_pasivo_id')->references('id')->on('plan_cuentas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('utilidads', function (Blueprint $table) {
            $table->dropForeign(['cuenta_pasivo_id']);
            $table->dropColumn('cuenta_pasivo_id');
        });
    }
}
