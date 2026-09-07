<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposParejaToCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->integer('conyuge_nivel_academico_id')->nullable()->after('conyugue_telefono');
            $table->date('conyuge_fecha_nacimiento')->nullable()->after('conyuge_nivel_academico_id');
            $table->string('conyuge_ocupacion', 60)->nullable()->after('conyuge_fecha_nacimiento');
            $table->string('conyuge_empresa_nombre', 120)->nullable()->after('conyuge_ocupacion');
            $table->string('conyuge_empresa_direccion', 80)->nullable()->after('conyuge_empresa_nombre');
            $table->string('conyuge_empresa_telefono', 20)->nullable()->after('conyuge_empresa_direccion');
            $table->string('conyuge_tiempo_empresa', 20)->nullable()->after('conyuge_empresa_telefono');
            $table->string('conyuge_cargo_empresa', 60)->nullable()->after('conyuge_tiempo_empresa');
        });
    }

    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('conyuge_nivel_academico_id');
            $table->dropColumn('conyuge_fecha_nacimiento');
            $table->dropColumn('conyuge_ocupacion');
            $table->dropColumn('conyuge_empresa_nombre');
            $table->dropColumn('conyuge_empresa_direccion');
            $table->dropColumn('conyuge_empresa_telefono');
            $table->dropColumn('conyuge_tiempo_empresa');
            $table->dropColumn('conyuge_cargo_empresa');
        });
    }
}
