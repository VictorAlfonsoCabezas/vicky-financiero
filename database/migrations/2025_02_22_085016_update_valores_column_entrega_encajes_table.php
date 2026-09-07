<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateValoresColumnEntregaEncajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrega_encajes', function (Blueprint $table) {
            $table->decimal('valor', 10, 5)->change();
            $table->decimal('porcentaje_retenido', 10, 5)->change();
            $table->decimal('valor_retenido', 10, 5)->change();
            $table->decimal('valor_entregado', 10, 5)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
