<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoCuentaTable extends Migration
{
    
    public function up()
    {
        Schema::create('tipo_cuenta', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(true);
            $table->string('nombre',30)->nullable();
            $table->string('apellido',30)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('tipo_cuenta');
    }
}
