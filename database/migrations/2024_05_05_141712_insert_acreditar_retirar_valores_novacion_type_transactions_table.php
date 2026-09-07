<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertAcreditarRetirarValoresNovacionTypeTransactionsTable extends Migration
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
                    'name' => 'INGRESAN VALORES DE PLAZO FIJO NOVACION',
                    'name_corto' => 'IFN',
                    'description' => 'SE INGRESAN VALORES DE NOVACION PARA UN PLAZO FIJO',
                    'action' => 'S',
                    'afecta' => 'E',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ],
                [
                    'company_id' => '1',
                    'name' => 'RETIRAN VALORES DE PLAZO FIJO NOVACION',
                    'name_corto' => 'RFN',
                    'description' => 'SE RETIRAN VALORES DE NOVACION PARA UN PLAZO FIJO',
                    'action' => 'S',
                    'afecta' => 'C',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ],
                [
                    'company_id' => '1',
                    'name' => 'DEPOSITO VALORES DE PLAZO FIJO NOVACION',
                    'name_corto' => 'DNN',
                    'description' => 'SE DEPOSITAN VALORES DE NOVACION PARA UN PLAZO FIJO',
                    'action' => 'R',
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
            DB::table('type_transactions')->where('name_corto', 'IFN')->delete();
        });
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('name_corto', 'RFN')->delete();
        });
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('name_corto', 'DNN')->delete();
        });
    }
}
