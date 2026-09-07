<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertSociosTypeTransactionsData extends Migration
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
                'name' => 'CARGA VALORES SOCIOS PRIMERA VEZ',
                'name_corto' => 'CVS',
                'description' => 'SE CARGA PROPRIMERA VEZ LOS VALORES DE LOS SOCIOS ',
                'action' => 'S',
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
