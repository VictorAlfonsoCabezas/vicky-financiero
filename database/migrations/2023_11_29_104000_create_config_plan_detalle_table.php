<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigPlanDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('config_plan_detalle', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('sede_id')->nullable();
            $table->integer('config_plan_header_id')->nullable();
            $table->integer('plan_cuentas_id')->nullable();
            $table->boolean('debe_haber')->default(true);
            $table->integer('operacion_id')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('config_plan_detalle');
    }
}
