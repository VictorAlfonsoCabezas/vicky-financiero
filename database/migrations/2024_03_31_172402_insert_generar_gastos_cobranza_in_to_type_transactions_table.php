<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertGenerarGastosCobranzaInToTypeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('to_type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->insert([
                [
                    'company_id' => '1',
                    'name' => 'GENERA GASTOS COBRANZA',
                    'name_corto' => 'GGC',
                    'description' => 'SE COBRA VALORES POR GASTOS DE COBRANZA',
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
        Schema::table('to_type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('name_corto', 'GGC')->delete();
        });
    }
}
