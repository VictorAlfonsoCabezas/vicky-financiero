<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodigoToCustomerTipoAhorros extends Migration
{
    public function up()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->string('codigo', 60)->after('tipo_ahorros_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('customer_tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
}
