<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhaEnviosTable extends Migration
{
    public function up()
    {
        Schema::create('wha_envios', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->boolean('envio_ahora')->default(0);
            $table->boolean('es_transacion')->default(0);
            $table->boolean('inmediato')->default(0);
            $table->string('description', 255)->nullable();
            $table->integer('type_transacction_id')->nullable();
            $table->integer('type_transacction_name')->nullable();
            $table->integer('proviene_id')->nullable();
            $table->integer('customer_id');
            $table->integer('customer_name');
            $table->integer('customer_celular');
            $table->string('whatsapp', 6000);
            $table->string('estado', 20)->default('PENDIENTE');
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_envio');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wha_envios');
    }
}
