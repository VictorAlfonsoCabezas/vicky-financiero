<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogSessionTable extends Migration
{
    public function up()
    {
        Schema::create('log_session', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('ip_address', 20);
            $table->string('description');
            $table->datetime('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_session');
    }
}
