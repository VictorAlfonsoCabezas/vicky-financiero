<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertLiquidacionCreditoToTypeTransactionsTable extends Migration
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
                    'company_id' => '1',
                    'name' => 'LIQUIDACION DE CREDITOS',
                    'name_corto' => 'LIC',
                    'description' => 'SE LIQUIDA EL CREDITO POR LOS VALORES TOTALES',
                    'action' => 'S',
                    'afecta' => 'E',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
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
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('name_corto', 'LIC')->delete();
        });
    }
}
