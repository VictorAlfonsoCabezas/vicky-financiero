<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertTransferenciasToTypeTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->insert([
                [
                    'company_id' => '1',
                    'name' => 'TRANSFERENCIAS ENVIA CLIENTE',
                    'name_corto' => 'TRE',
                    'description' => 'TRANSFERENCIAS ENVIA ELCLIENTE, ES DECIR EL VALOR QUE ENVIA EL CLIENTE A OTRO CLIENTE',
                    'action' => 'R',
                    'afecta' => 'C',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ],
                [
                    'company_id' => '1',
                    'name' => 'TRANSFERENCIAS RECIBE CLIENTE',
                    'name_corto' => 'TRR',
                    'description' => 'TRANSFERENCIAS RECIBE EL CLIENTE, ES DECIR EL VALOR QUE ENVIA EL CLIENTE A OTRO CLIENTE',
                    'action' => 'S',
                    'afecta' => 'C',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ],
            ]);
        });
    }

    public function down()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('name_corto', 'TRE')->delete();
            DB::table('type_transactions')->where('name_corto', 'TRR')->delete();
        });
    }
}
