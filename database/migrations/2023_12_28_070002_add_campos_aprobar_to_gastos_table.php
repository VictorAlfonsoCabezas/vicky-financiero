<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposAprobarToGastosTable extends Migration
{
    public function up()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->boolean('deducible')->after('gastos_categorias_id')->default(false);
            $table->integer('proveedor_id')->after('deducible')->nullable();
            $table->integer('user_rechaza_id')->after('user_aprueba_id')->nullable();
            $table->string('razon_rechaza')->after('user_rechaza_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropColumn('deducible');
            $table->dropColumn('proveedor_id');
            $table->dropColumn('user_rechaza_id');
            $table->dropColumn('razon_rechaza');
        });
    }
}
