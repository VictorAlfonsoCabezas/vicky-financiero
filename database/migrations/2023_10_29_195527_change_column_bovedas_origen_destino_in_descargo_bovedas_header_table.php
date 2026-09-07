<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnBovedasOrigenDestinoInDescargoBovedasHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->integer('boveda_origen_id')->nullable()->change();
            $table->integer('boveda_destino_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->integer('boveda_origen_id')->nullable(false)->change();
            $table->integer('boveda_destino_id')->nullable(false)->change();
        });
    }
}
