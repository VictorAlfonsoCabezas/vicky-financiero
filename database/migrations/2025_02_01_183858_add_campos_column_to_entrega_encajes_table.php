<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposColumnToEntregaEncajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrega_encajes', function (Blueprint $table) {
            $table->integer('user_cancel_id')->nullable()->after('user_created_name');
            $table->string('user_cancel_name', 50)->nullable()->after('user_cancel_id');
            $table->date('date_cancel')->nullable()->after('user_cancel_name');
            $table->time('hour_cancel')->nullable()->after('date_cancel');
            $table->integer('user_entrega_id')->nullable()->after('hour_cancel');
            $table->string('user_entrega_name', 50)->nullable()->after('user_entrega_id');
            $table->date('date_entrega')->nullable()->after('user_entrega_name');
            $table->time('hour_entrega')->nullable()->after('date_entrega');
            $table->string('status', 50)->nullable()->default('PENDIENTE')->after('hour_entrega');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrega_encajes', function (Blueprint $table) {
            $table->dropColumn('user_cancel_id');
            $table->dropColumn('user_cancel_name');
            $table->dropColumn('date_cancel');
            $table->dropColumn('hour_cancel');
            $table->dropColumn('user_entrega_id');
            $table->dropColumn('user_entrega_name');
            $table->dropColumn('date_entrega');
            $table->dropColumn('hour_entrega');
            $table->dropColumn('status');
        });
    }
}
