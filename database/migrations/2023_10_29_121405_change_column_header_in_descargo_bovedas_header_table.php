<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnHeaderInDescargoBovedasHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->integer('company_id')->change();
            $table->integer('operaciones_descargo_bovedas_id')->change();
            $table->integer('boveda_origen_id')->change();
            $table->integer('boveda_destino_id')->change();
            $table->string('estado', 20)->default('PENDIENTE')->change();
        });
    }

    public function down()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->string('company_id')->change();
            $table->string('operaciones_descargo_bovedas_id')->change();
            $table->string('boveda_origen_id')->change();
            $table->string('boveda_destino_id')->change();
            $table->boolean('estado')->change();
        });
    }
}
