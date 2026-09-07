<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EliminarCamposDeTabla extends Migration
{
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn([
                'category_product_id',
                'unit_measure_id',
                'unidad_name',
                'tipo_iva',
                'promotion',
                'stock',
                'stock_minimo',
                'lotes',
            ]);
        });
    }

    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
        });
    }
}
