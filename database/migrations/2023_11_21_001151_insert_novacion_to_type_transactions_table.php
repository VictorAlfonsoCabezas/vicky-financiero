<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertNovacionToTypeTransactionsTable extends Migration
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
                    'name' => 'PAGO DE VALORES POR NOVACION',
                    'name_corto' => 'NOV',
                    'description' => 'SE PAGA VALORES DE NOVACION DE UN CREDITO',
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
            DB::table('type_transactions')->where('name_corto', 'SOLICITUD DE ACREDITACION EN CUENTA')->delete();
        });
    }
}
