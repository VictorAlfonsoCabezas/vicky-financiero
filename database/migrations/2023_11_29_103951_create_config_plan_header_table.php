<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigPlanHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('config_plan_header', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('type_transaction_id')->nullable();
            $table->integer('concepto_id')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('config_plan_header');
    }
}
