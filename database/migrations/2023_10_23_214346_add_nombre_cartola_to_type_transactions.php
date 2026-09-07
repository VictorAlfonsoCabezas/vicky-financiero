<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreCartolaToTypeTransactions extends Migration {

    public function up() {
        Schema::table('type_transactions', function (Blueprint $table) {
            $table->string('nombre_cartola')->after('name_corto')->nullable();
        });
    }

    public function down() {
        Schema::table('type_transactions', function (Blueprint $table) {
            $table->dropColumn('nombre_cartola');
        });
    }

}
