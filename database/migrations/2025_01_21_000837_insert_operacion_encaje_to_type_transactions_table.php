<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertOperacionEncajeToTypeTransactionsTable extends Migration
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
                    'company_id' => 1,
                    'name' => 'INGRESO DE RETENCION DE ENCAJE',
                    'name_corto' => 'IRE',
                    'description' => 'INGRESA A LA CAJA EL VALOR DE RETENCION DEL ENCAJE DE UNA CUENTA',
                    'action' => 'S',
                    'afecta' => 'E',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ]
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
            DB::table('type_transactions')->where('name_corto', 'IRE')->delete();
        });
    }
}
