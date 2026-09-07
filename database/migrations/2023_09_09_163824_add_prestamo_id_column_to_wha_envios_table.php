<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrestamoIdColumnToWhaEnviosTable extends Migration {

    public function up() {
        Schema::table('wha_envios', function (Blueprint $table) {
           $table->string('prestamo_id')->nullable()->after('fecha_envio');
        });
    }

    public function down() {
        Schema::table('wha_envios', function (Blueprint $table) {
           $table->dropColumn('prestamo_id');
        });
    }

}
