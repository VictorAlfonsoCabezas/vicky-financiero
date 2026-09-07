<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataToTipoRetencionTable extends Migration
{
    public function up()
    {
        Schema::table('tipo_retencion', function (Blueprint $table) {
            DB::table('tipo_retencion')->insert([
                [
                    'id' => '1',
                    'name' => 'FUENTE',
                    'status' => true,
                ],
                [
                    'id' => '2',
                    'name' => 'IVA',
                    'status' => true,
                ]
            ]);
        });
    }

    public function down()
    {
        Schema::table('tipo_retencion', function (Blueprint $table) {
            //
        });
    }
}
