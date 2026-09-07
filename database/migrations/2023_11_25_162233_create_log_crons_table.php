<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogCronsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_crons', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('cron_lists_id')->nullable();
            $table->integer('credit_header_id')->nullable();
            $table->integer('credit_detail_id')->nullable();
            $table->string('detalle')->nullable();
            $table->date('date_create')->nullable();
            $table->time('hour_create')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_crons');
    }
}
