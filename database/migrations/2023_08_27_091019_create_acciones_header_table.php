<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccionesHeaderTable extends Migration
{
    public function up()
    {
        Schema::create('acciones_header', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('nombre', 60)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->decimal('capital')->default('0.00');
            $table->integer('user_created')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acciones_header');
    }
}
