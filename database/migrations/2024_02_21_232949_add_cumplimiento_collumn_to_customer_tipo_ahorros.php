<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCumplimientoCollumnToCustomerTipoAhorros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->boolean('cumplimiento')->after('dias_plazo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('cumplimiento');
        });
    }
}
