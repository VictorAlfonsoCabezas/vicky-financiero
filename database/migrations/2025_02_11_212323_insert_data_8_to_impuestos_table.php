<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertData8ToImpuestosTable extends Migration
{
    public function up()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            Schema::table('impuestos', function (Blueprint $table) {
                DB::table('impuestos')->insert([
                    [
                        'id' => 5,
                        'company_id' => 1,
                        'nombre' => 'IVA 8%',
                        'descripcion' => 'IVA 8% del SRI',
                        'valor' => 8.00,
                        'codigo' => 8,
                        'por_defecto' => false,
                        'status' => true,
                    ],

                ]);
            });
        });
    }

    public function down()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            DB::table('impuestos')->where('id', 5)->delete();
        });
    }
}
