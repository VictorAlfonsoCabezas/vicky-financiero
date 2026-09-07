<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComprobanteColumnToCustomerMovimientos extends Migration {

    public function up() {
        Schema::table('customer_movimientos', function (Blueprint $table) {
            $table->string('comprobante')->after('code')->nullable();
        });
    }

    public function down() {
        Schema::table('customer_movimientos', function (Blueprint $table) {
           $table->dropColumn('comprobante');
        });
    }
}
