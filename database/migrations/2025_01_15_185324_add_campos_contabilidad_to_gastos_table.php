<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposContabilidadToGastosTable extends Migration
{
    public function up()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->integer('sustento_tributario_id')->nullable()->after('gastos_categorias_id');
            $table->integer('tipo_comprobante_id')->nullable()->after('sustento_tributario_id');
            $table->string('numero', 50)->nullable()->after('tipo_comprobante_id');
            $table->string('establecimiento', 50)->nullable()->after('numero');
            $table->string('punto_emision', 50)->nullable()->after('establecimiento');
            $table->string('autorizacion', 50)->nullable()->after('punto_emision');
            $table->date('fecha_autorizacion')->nullable();
        });
    }

    public function down()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropColumn('sustento_tributario_id');
            $table->dropColumn('tipo_comprobante_id');
            $table->dropColumn('numero');
            $table->dropColumn('establecimiento');
            $table->dropColumn('punto_emision');
            $table->dropColumn('autorizacion');
            $table->dropColumn('fecha_autorizacion');
        });
    }
}
