<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntregaEncajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrega_encajes', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('customer_tipo_ahorro_id');
            $table->decimal('valor', 8,2)->default('0.00');
            $table->decimal('porcentaje_retenido', 8,2)->default('0.00');
            $table->decimal('valor_retenido', 8,2)->default('0.00');
            $table->decimal('valor_entregado', 8,2)->default('0.00');
            $table->date('date_created')->nullable();
            $table->time('hour_created')->nullable();
            $table->integer('user_created_id')->nullable();
            $table->string('user_created_name')->nullable();
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
        Schema::dropIfExists('entrega_encajes');
    }
}
