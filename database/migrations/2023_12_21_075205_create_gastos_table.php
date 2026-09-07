<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('descripcion')->nullable();
            $table->decimal('valor', 8, 2)->nullable();
            $table->integer('user_responsable_id')->nullable();
            $table->integer('user_aprueba_id')->nullable();
            $table->boolean('pagado_compania_empleado')->default(true);
            $table->string('estado')->default('PENDIENTE');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos');
    }
}
