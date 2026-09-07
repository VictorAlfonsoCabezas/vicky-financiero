<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperacionesDescargoBovedasIdToConfigPlanHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('config_plan_header', function (Blueprint $table) {
            $table->integer('operaciones_descargo_bovedas_id')->after('type_transaction_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('config_plan_header', function (Blueprint $table) {
            $table->dropColumn('operaciones_descargo_bovedas_id');
        });
    }
}
