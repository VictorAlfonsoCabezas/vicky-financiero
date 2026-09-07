<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertTypeTransactionsData extends Migration {

    public function up() {
        DB::table('type_transactions')->insert([
            [
                'company_id' => '1',
                'name' => 'NOTIFICACION COBRO',
                'name_corto' => 'NTC',
                'description' => 'SE NOTIFICA EL COBRO DE UNA LETRA VENCIDA',
                'action' => 'S',
                'afecta' => 'C',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
            ]
        ]);
    }

    public function down() {
        //
    }

}
