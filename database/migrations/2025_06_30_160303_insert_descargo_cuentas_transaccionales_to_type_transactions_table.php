<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertDescargoCuentasTransaccionalesToTypeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->insert([
                [
                    'company_id' => 1,
                    'name' => 'DESCARGO DE CUENTAS TRANSACCIONALES',
                    'name_corto' => 'DCT',
                    'description' => 'SE DESCARGA LOS VALORES PARAMETRIZADOS DE LAS CUENTAS TRANSACCIONALES',
                    'action' => 'R',
                    'afecta' => 'C',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ]
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
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('id', 37)->delete();
        });
    }
}
