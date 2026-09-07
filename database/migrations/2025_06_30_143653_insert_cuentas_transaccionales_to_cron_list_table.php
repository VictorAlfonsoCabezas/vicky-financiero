<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertCuentasTransaccionalesToCronListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_lists', function (Blueprint $table) {
            DB::table('cron_lists')->insert([
                [
                    'company_id' => '1',
                    'name' => 'DESCARGO DE CUENTAS TRANSACCIONALES',
                    'detalle' => 'HACE un movimiento de descargo de las cuentas transaccionales',
                    'cron' => 'descargo_cuentas_transaccionales',
                    'status' => 1,
                ],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_lists', function (Blueprint $table) {
             DB::table('cron_lists')->where('cron', 'descargo_cuentas_transaccionales')->delete();
        });
    }
}
