<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertGastosInicialesToOpereacionesDescargoBovedasTable extends Migration
{
    public function up()
    {
        DB::table('operaciones_descargo_bovedas')->insert([
            [
                'id' => '8',
                'company_id' => '1',
                'nombre' => 'GASTO INICIAL EMPRESA',
                'descripcion' => 'Gasto inicial empresa',
                'nombre_corto' => 'GASINI',
                'afecta' => 'E',
                'accion' => 'R',
                'status' => 1,
            ],
        ]);
    }

    public function down()
    {
    }
}
