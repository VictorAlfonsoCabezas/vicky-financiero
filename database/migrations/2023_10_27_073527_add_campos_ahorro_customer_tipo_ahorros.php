<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposAhorroCustomerTipoAhorros extends Migration
{
    public function up()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->after('codigo')->default(0.00);
            $table->integer('tipo_ahorros_programados_detalle_id')->after('valor')->nullable();
            $table->string('pago', 20)->after('tipo_ahorros_programados_detalle_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('valor');
            $table->dropColumn('tipo_ahorros_programados_detalle_id');
            $table->dropColumn('pago');
        });
    }
}
