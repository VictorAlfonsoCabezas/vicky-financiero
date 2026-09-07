<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnDetalleInDescargoBovedasDetalleTable extends Migration
{
    public function up()
    {
        Schema::table('descargo_bovedas_detalle', function (Blueprint $table) {
            $table->integer('company_id')->change();
            $table->integer('boveda_header_id')->change();
        });
    }

    public function down()
    {
        Schema::table('descargo_bovedas_detalle', function (Blueprint $table) {
            $table->string('company_id')->change();
            $table->string('boveda_header_id')->change();
        });
    }
}
