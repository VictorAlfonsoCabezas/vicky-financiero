<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeInOperacionesDescargoBovedasTable extends Migration
{
    public function up()
    {
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {
            $table->string('nombre', 60)->change();
            $table->string('descripcion', 255)->change();
            $table->string('nombre_corto', 20)->change();
        });
    }

    public function down()
    {
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {
            $table->integer('nombre')->change();
            $table->integer('descripcion')->change();
            $table->integer('nombre_corto')->change();
        });
    }
}
