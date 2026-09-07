<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposTablasToConfigPlanDetalleTable extends Migration
{
    public function up()
    {
        Schema::table('config_plan_detalle', function (Blueprint $table) {
            $table->string('campo_foraneo', 60)->after('operacion_id')->nullable();
            $table->string('tabla', 60)->after('campo_foraneo')->nullable();
            $table->string('campo', 60)->after('tabla')->nullable();
        });
    }

    public function down()
    {
        Schema::table('config_plan_detalle', function (Blueprint $table) {
            $table->dropColumn('campo_foraneo');
            $table->dropColumn('tabla');
            $table->dropColumn('campo');
        });
    }
}
