<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValorDefectoToAccionesValoresTable extends Migration
{
    public function up()
    {
        Schema::table('acciones_valores', function (Blueprint $table) {
            $table->decimal('valor_defecto', 8,2)->default(0.00)->after('operacion');
        });
    }

    public function down()
    {
        Schema::table('acciones_valores', function (Blueprint $table) {
            $table->dropColumn('valor_defecto');
        });
    }
}
