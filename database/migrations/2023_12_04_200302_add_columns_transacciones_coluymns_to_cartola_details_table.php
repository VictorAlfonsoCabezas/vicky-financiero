<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTransaccionesColuymnsToCartolaDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cartola_details', function (Blueprint $table) {
            $table->integer('cartola_headers_id')->after('cartola_header_code')->nullable();
            $table->string('cara')->after('cartola_header_code')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cartola_details', function (Blueprint $table) {
            $table->dropColumn('cartola_headers_id');
            $table->dropColumn('cara');
        });
    }
}
