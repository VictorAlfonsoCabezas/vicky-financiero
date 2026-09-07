<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposCustomerEmpresaToCustomerTable extends Migration
{

    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('empresa_nombre', 60)->nullable()->after('conyuge_cargo_empresa');
            $table->string('empresa_direccion', 60)->nullable()->after('empresa_nombre');
            $table->integer('empresa_provincia_id')->nullable()->after('empresa_direccion');
            $table->integer('empresa_canton_id')->nullable()->after('empresa_provincia_id');
            $table->integer('empresa_parroquia_id')->nullable()->after('empresa_canton_id');
            $table->string('empresa_telefono', 20)->nullable()->after('empresa_parroquia_id');
            $table->string('empresa_tiempo', 6)->nullable()->after('empresa_telefono');
            $table->string('empresa_cargo', 20)->nullable()->after('empresa_tiempo');
            $table->string('negocio_nombre', 60)->nullable()->after('empresa_cargo');
            $table->string('negocio_direccion', 60)->nullable()->after('negocio_nombre');
            $table->integer('negocio_provincia_id')->nullable()->after('negocio_direccion');
            $table->integer('negocio_canton_id')->nullable()->after('negocio_provincia_id');
            $table->integer('negocio_parroquia_id')->nullable()->after('negocio_canton_id');
            $table->string('negocio_telefono', 60)->nullable()->after('negocio_parroquia_id');
            $table->string('negocio_tiempo', 60)->nullable()->after('negocio_telefono');
            $table->string('negocio_actividad', 60)->nullable()->after('negocio_tiempo');
        });
    }


    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('empresa_nombre');
            $table->dropColumn('empresa_direccion');
            $table->dropColumn('empresa_provincia_id');
            $table->dropColumn('empresa_canton_id');
            $table->dropColumn('empresa_parroquia_id');
            $table->dropColumn('empresa_telefono');
            $table->dropColumn('empresa_tiempo');
            $table->dropColumn('empresa_cargo');
            $table->dropColumn('negocio_nombre');
            $table->dropColumn('negocio_direccion');
            $table->dropColumn('negocio_provincia_id');
            $table->dropColumn('negocio_canton_id');
            $table->dropColumn('negocio_parroquia_id');
            $table->dropColumn('negocio_telefono');
            $table->dropColumn('negocio_tiempo');
            $table->dropColumn('negocio_actividad');
        });
    }
}
