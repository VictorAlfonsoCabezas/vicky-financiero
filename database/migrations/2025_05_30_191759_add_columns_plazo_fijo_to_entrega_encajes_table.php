<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPlazoFijoToEntregaEncajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entrega_encajes', function (Blueprint $table) {
            $table->decimal('porcentaje_plazo_fijo', 8,2)->default('0.00')->after('hour_entrega');
            $table->decimal('valor_plazo_fijo', 8,2)->default('0.00')->after('porcentaje_plazo_fijo');
            $table->integer('taza_plazo_fijo')->nullable()->after('valor_plazo_fijo');
            $table->string('pago_plazo_fijo')->nullable()->after('taza_plazo_fijo');
            $table->integer('dias_plazo_fijo')->nullable()->after('pago_plazo_fijo');
            $table->integer('beneficiario_plazo_fijo')->nullable()->after('dias_plazo_fijo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entrega_encajes', function (Blueprint $table) {
            $table->dropColumn('porcentaje_plazo_fijo');
            $table->dropColumn('valor_plazo_fijo');
            $table->dropColumn('taza_plazo_fijo');
            $table->dropColumn('pago_plazo_fijo');
            $table->dropColumn('dias_plazo_fijo');
            $table->dropColumn('beneficiario_plazo_fijo');
        });
    }
}
