<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigPlanHeaderIdToAsientosHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->unsignedBigInteger('config_plan_header_id')->nullable()->after('concepto_id');
            $table->foreign('config_plan_header_id')->references('id')->on('config_plan_header')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropForeign(['config_plan_header_id']);
            $table->dropColumn('config_plan_header_id');
        });
    }
}
