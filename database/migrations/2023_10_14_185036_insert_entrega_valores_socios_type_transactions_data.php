<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEntregaValoresSociosTypeTransactionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('type_transactions')->insert([
            [
                'company_id' => '1',
                'name' => 'ENTREGA UTILIDAD AL SOCIO',
                'name_corto' => 'EUS',
                'description' => 'SE ENTREGA UTILIDAD AL SOCIO',
                'action' => 'R',
                'afecta' => 'E',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
