<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataToImpuestosTable extends Migration
{
    public function up()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            DB::table('impuestos')->insert([
                [
                    'id' => 1,
                    'company_id' => 1,
                    'nombre' => 'IVA 0%',
                    'descripcion' => 'IVA 0% del SRI',
                    'valor' => 0.00,
                    'codigo' => 0,
                    'por_defecto' => false,
                    'status' => true,
                ],
                [
                    'id' => 2,
                    'company_id' => 1,
                    'nombre' => 'IVA 5%',
                    'descripcion' => 'IVA 5% del SRI',
                    'valor' => 5.00,
                    'codigo' => 5,
                    'por_defecto' => false,
                    'status' => true,
                ],
                [
                    'id' => 3,
                    'company_id' => 1,
                    'nombre' => 'IVA 12%',
                    'descripcion' => 'IVA 12% del SRI',
                    'valor' => 12.00,
                    'codigo' => 2,
                    'por_defecto' => false,
                    'status' => true,
                ],
                [
                    'id' => 4,
                    'company_id' => 1,
                    'nombre' => 'IVA 15%',
                    'descripcion' => 'IVA 15% del SRI',
                    'valor' => 15.00,
                    'codigo' => 4,
                    'por_defecto' => true,
                    'status' => true,
                ],
                
            ]);
        });
    }

    public function down()
    {
        Schema::table('impuestos', function (Blueprint $table) {
            //
        });
    }
}
