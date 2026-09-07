<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertAccionesAutomaticasToTypeTransactionsTable extends Migration
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
                'name' => 'DEBITO AUTOMATICO',
                'name_corto' => 'DEA',
                'description' => 'SE DEBITA EL VALOR DE LA LETRA DE CREDITO AUTOMATICAMENTE',
                'action' => 'R',
                'afecta' => 'C',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
            ],
            [
                'company_id' => '1',
                'name' => 'PAGO CREDITOS AUTOMATICO',
                'name_corto' => 'PCA',
                'description' => 'PAGO DE CREDITOS AUTOMATICOS POR DE LA CUENTA DEL CLIENTE',
                'action' => 'S',
                'afecta' => 'C',
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
        Schema::table('type_transactions', function (Blueprint $table) {
            //
        });
    }
}
