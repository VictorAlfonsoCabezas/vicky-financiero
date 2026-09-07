<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsertPagoProveedoresTypeTransactionsData extends Migration
{
    public function up()
    {
        DB::table('type_transactions')->insert([
            [
                'company_id' => '1',
                'name' => 'PAGO PROVEEDORES',
                'name_corto' => 'PPR',
                'description' => 'PAGO PARA LOS PROVEEDORES',
                'action' => 'R',
                'afecta' => 'E',
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:m:s'),
                'created_at' => date('Y-m-d H:m:s'),
                'updated_at' => date('Y-m-d H:m:s'),
            ]
        ]);
    }

    public function down()
    {
        //
    }
}
