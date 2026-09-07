<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEliminaUtilidadesTypeTransactionsData extends Migration
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
                'name' => 'REVERSA UTILIDAD',
                'name_corto' => 'RU',
                'description' => 'ELIMINA LOS DATOS PARA CALCULAR LAS UTILIDADES',
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
