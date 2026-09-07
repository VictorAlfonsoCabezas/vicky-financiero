<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValoresGastosToGastosTable extends Migration
{
    public function up()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->boolean('tiene_impuestos')->default(false)->after('gastos_categorias_id');
            $table->decimal('descuento', 8, 2)->default('0.00')->after('tiene_impuestos');
            $table->decimal('subtotal_descuento', 8, 2)->default('0.00')->after('descuento');
            $table->decimal('subtotal_exento', 8, 2)->default('0.00')->after('subtotal_descuento');
            $table->decimal('suma_subtotal', 8, 2)->default('0.00')->after('subtotal_exento');
            $table->decimal('suma_iva', 8, 2)->default('0.00')->after('suma_subtotal');
            $table->decimal('total', 8, 2)->default('0.00')->after('suma_iva');
        });
    }

    public function down()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->dropColumn('tiene_impuestos');
            $table->dropColumn('descuento');
            $table->dropColumn('subtotal_descuento');
            $table->dropColumn('subtotal_exento');
            $table->dropColumn('suma_subtotal');
            $table->dropColumn('suma_iva');
            $table->dropColumn('total');
        });
    }
}
