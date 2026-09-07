<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToGastosTable extends Migration
{
    public function up()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->integer('gastos_categorias_id')->after('company_id')->nullable();
            $table->datetime('fecha_creacion')->after('descripcion')->nullable();
            $table->datetime('fecha_aprobacion')->after('fecha_creacion')->nullable();
        });
    }

    public function down()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropColumn('gastos_categorias_id');
            $table->dropColumn('fecha_creacion');
            $table->dropColumn('fecha_aprobacion');
        });
    }
}
