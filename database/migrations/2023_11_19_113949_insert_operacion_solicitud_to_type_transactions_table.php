<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertOperacionSolicitudToTypeTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->insert([
                [
                    'company_id' => '1',
                    'name' => 'SOLICITUD DE ACREDITACION EN CUENTA',
                    'name_corto' => 'SOL',
                    'description' => 'SOLICITUD DE ACREDITACION EN CUENTA, DESDE LA VISTA DE CLIENTES',
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
            //
        });
    }
}
