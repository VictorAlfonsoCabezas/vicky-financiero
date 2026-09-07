<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteresFijoParametrizadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interes_fijo_parametrizado', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor_inicio', 8,2)->default(0);
            $table->decimal('valor_fin', 8,2)->default(0);
            $table->decimal('primer_valor', 8,2)->default(0);
            $table->decimal('segundo_valor', 8,2)->default(0);
            $table->decimal('tercer_valor', 8,2)->default(0);
            $table->decimal('cuarto_valor', 8,2)->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('interes_fijo_parametrizado');
    }
}
