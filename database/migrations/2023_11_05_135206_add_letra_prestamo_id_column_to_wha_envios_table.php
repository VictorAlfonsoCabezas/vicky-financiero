<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLetraPrestamoIdColumnToWhaEnviosTable extends Migration {

    public function up() {
        Schema::table('wha_envios', function (Blueprint $table) {
            $table->integer('letra_prestamo_id')->nullable()->after('prestamo_id');
        });
    }

    public function down() {
        Schema::table('wha_envios', function (Blueprint $table) {
            $table->dropColumn('letra_prestamo_id');
        });
    }
}
