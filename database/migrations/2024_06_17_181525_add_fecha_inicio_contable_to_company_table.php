<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaInicioContableToCompanyTable extends Migration
{
    public function up()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->date('fecha_inicio_contable')->after('penalidad_plazo_fijo')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('fecha_inicio_contable');
        });
    }
}
