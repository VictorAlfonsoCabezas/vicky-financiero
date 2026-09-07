<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertAgregarTransaccionTypeTransactionsData extends Migration
{
    public function up()
    {
        DB::table('type_transactions')->insert([
            [
                'company_id' => '1',
                'name' => 'SUMA EMPRESA',
                'name_corto' => 'SE',
                'description' => 'SUMA A EMPRESA EL VALOR',
                'action' => 'S',
                'afecta' => 'E',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
            ],
            [
                'company_id' => '1',
                'name' => 'SUMA CLIENTE',
                'name_corto' => 'SC',
                'description' => 'SUMA A CLIENTE EL VALOR',
                'action' => 'S',
                'afecta' => 'C',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
            ]
        ]);
    }

    public function down()
    {
    }
}
