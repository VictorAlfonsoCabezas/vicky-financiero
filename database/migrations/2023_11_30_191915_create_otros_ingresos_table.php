<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtrosIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otros_ingresos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->integer('movimiento_id')->nullable();
            $table->string('name')->nullable();
            $table->string('descripcion')->nullable();
            $table->decimal('valor', 8,2)->default(0);
            $table->date('date_create')->nullable();
            $table->time('hour_create')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
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
        Schema::dropIfExists('otros_ingresos');
    }
}
