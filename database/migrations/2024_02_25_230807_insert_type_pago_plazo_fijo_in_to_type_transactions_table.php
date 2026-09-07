<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertTypePagoPlazoFijoInToTypeTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('type_transactions', function (Blueprint $table) {
            DB::table('type_transactions')->insert([
                [
                    'company_id' => '1',
                    'name' => 'PAGO DE VALORES PLAZO FIJO',
                    'name_corto' => 'PVPF',
                    'description' => 'SE PAGAN VALORES POR PLAZO FIJO A LOS CLIENTES',
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
            DB::table('type_transactions')->where('name_corto', 'PVPF')->delete();
            
        });
    }
}
