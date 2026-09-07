<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPorcentajeToOperacionTable extends Migration
{
    public function up()
    {
        Schema::table('operacion', function (Blueprint $table) {
            $table->decimal('porcentaje', 8, 2)->after('descripcion')->default(100.00);
        });
    }

    public function down()
    {
        Schema::table('operacion', function (Blueprint $table) {
            $table->dropColumn('porcentaje');
        });
    }
}
