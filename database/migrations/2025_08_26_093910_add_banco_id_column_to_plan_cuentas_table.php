<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBancoIdColumnToPlanCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->integer('banco_id')->nullable()->after('prestamo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_cuentas', function (Blueprint $table) {
            $table->dropColumn('banco_id');
        });
    }
}
