<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoDiasMoraToCompany extends Migration {

    public function up() {
        Schema::table('company', function (Blueprint $table) {
            $table->string('select_tipo_interes', 2)->default('M')->after('status');
        });
    }

    public function down() {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('select_tipo_interes');
        });
    }

}
