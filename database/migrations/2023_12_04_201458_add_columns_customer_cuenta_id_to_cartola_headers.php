<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsCustomerCuentaIdToCartolaHeaders extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cartola_headers', function (Blueprint $table) {
            $table->integer('customer_tipo_ahorro_id')->after('numero')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cartola_headers', function (Blueprint $table) {
            $table->dropColumn('customer_tipo_ahorro_id');
        });
    }
}
