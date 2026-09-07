<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescargoBovedasHeaderIdToAsientosHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->integer('descargo_bovedas_header_id')->after('customer_movimiento_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asientos_header', function (Blueprint $table) {
            $table->dropColumn('descargo_bovedas_header_id');
        });
    }
}
