<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeaderCalculadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('header_calculadoras', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('customer_id');
            $table->integer('customer_movimientos_id');
            $table->string('tipo_pago', 15);
            $table->integer('tipo_ahorros_programados_detalle_id');
            $table->decimal('interes', 8, 2)->default(0);
            $table->integer('dias_plazo');
            $table->decimal('rentabilidad', 8, 2)->default(0);
            $table->decimal('penalizado', 8, 2)->default(0);
            $table->decimal('total', 8, 2)->default(0);
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
        Schema::dropIfExists('header_calculadoras');
    }
}
