<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToTipoAhorros extends Migration
{
    public function up()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->boolean('cuenta_certificado')->after('company_id')->default(false);
            $table->decimal('cuenta_certificado_valor_max', 8, 2)->after('cuenta_certificado')->default('0.00');
        });
    }


    public function down()
    {
        Schema::table('tipo_ahorros', function (Blueprint $table) {
            $table->dropColumn('cuenta_certificado');
            $table->dropColumn('cuenta_certificado_valor_max');
        });
    }
}
