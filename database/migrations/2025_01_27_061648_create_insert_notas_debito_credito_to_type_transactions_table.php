<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsertNotasDebitoCreditoToTypeTransactionsTable extends Migration
{
    public function up()
    {
        DB::table('type_transactions')->insert([
            [
                'company_id' => '1',
                'name' => 'DESPOSITOS NOTA DE CREDITO',
                'name_corto' => 'DNC',
                'nombre_cartola' => 'DNC',
                'description' => 'DESPOSITOS NOTA DE CREDITO, SE GENERA UN APROBACION PREVIA PARA REGISTRAR EN LA CUENTA',
                'action' => 'S',
                'afecta' => 'C',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
                'created_at' => date('Y-m-d H:m:s'),
                'updated_at' => date('Y-m-d H:m:s'),
            ],
            [
                'company_id' => '1',
                'name' => 'RETIRO NOTA DE DEBITO',
                'name_corto' => 'DND',
                'nombre_cartola' => 'DND',
                'description' => 'RETIRO NOTA DE DEBITO, SE RETIRA DE LA CUENTA BAJO UN CONCEPTO INTERNO',
                'action' => 'R',
                'afecta' => 'C',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
                'created_at' => date('Y-m-d H:m:s'),
                'updated_at' => date('Y-m-d H:m:s'),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('type_transactions');
    }
}
