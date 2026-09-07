<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentosParametrizablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_parametrizables', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('formato')->nullable();
            $table->longText('content')->nullable();
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('documentos_parametrizables');
    }
}
