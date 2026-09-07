<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiasGraciaColmunToCompanyTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('company', function (Blueprint $table) {
           $table->integer('dias_gracia')->default(0)->after('dias_inicio_cobro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('company', function (Blueprint $table) {
           $table->dropColumn('dias_gracia');
        });
    }

}
