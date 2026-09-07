<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosPlanCuentasTable extends Migration
{
    public function up()
    {
        Schema::create('gastos_plan_cuentas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('gasto_id')->nullable();
            $table->integer('plan_cuentas_id')->nullable();
            $table->integer('centro_costos_id')->nullable();
            $table->decimal('valor', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos_plan_cuentas');
    }
}
