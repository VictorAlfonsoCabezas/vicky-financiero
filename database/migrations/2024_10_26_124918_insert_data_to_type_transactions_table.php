<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataToTypeTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->insert([
                [
                    'id' => 37,
                    'company_id' => 1,
                    'name' => 'DEPOSITOS PLAZO FIJO',
                    'name_corto' => 'DFJ',
                    'description' => 'SE DEPOSITA VALORES A PLAZO FIJO VALOR INICIAL',
                    'action' => 'S',
                    'afecta' => 'C',
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:m:s'),
                ]
            ]);
        });
    }

    public function down()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->where('id', 37)->delete();
        });
    }
}
