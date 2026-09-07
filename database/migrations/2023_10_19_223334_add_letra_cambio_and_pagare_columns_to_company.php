<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLetraCambioAndPagareColumnsToCompany extends Migration {

    public function up() {
        Schema::table('company', function (Blueprint $table) {
            $table->boolean('letra_cambio')->after('dias_gracia')->default(0);
            $table->boolean('pagare')->after('dias_gracia')->default(0);
        });
    }

    public function down() {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('letra_cambio');
            $table->dropColumn('pagare');
        });
    }

}
