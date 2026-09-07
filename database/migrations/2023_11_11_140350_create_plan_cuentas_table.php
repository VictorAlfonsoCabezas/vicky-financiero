<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanCuentasTable extends Migration
{
    public function up()
    {
        Schema::create('plan_cuentas', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre', 60)->nullable();
            $table->string('codigo', 60)->nullable();
            $table->string('nivel1', 2)->nullable();
            $table->string('nivel2', 2)->nullable();
            $table->string('nivel3', 2)->nullable();
            $table->string('nivel4', 2)->nullable();
            $table->string('nivel5', 2)->nullable();
            $table->string('nivel6', 2)->nullable();
            $table->string('nivel7', 2)->nullable();
            $table->decimal('saldo', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_cuentas');
    }
}
