<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposViviendaToCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('telefono_fijo', 25)->nullable()->after('telefono_3');
            $table->integer('cargas_familiares')->nullable()->default(0)->after('estado_civil');
            $table->boolean('seperacion_bienes')->nullable()->default(false)->after('cargas_familiares');
            $table->string('tipo_vivienda', 255)->nullable()->after('seperacion_bienes');
            $table->integer('tiempo_vivienda')->nullable()->after('tipo_vivienda');
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('telefono_fijo');
            $table->dropColumn('cargas_familiares');
            $table->dropColumn('seperacion_bienes');
            $table->dropColumn('tipo_vivienda');
            $table->dropColumn('tiempo_vivienda');
        });
    }
}
