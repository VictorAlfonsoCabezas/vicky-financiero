<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertValuesToTable extends Migration
{
    public function up()
    {
        DB::table('operaciones_descargo_bovedas')->insert([
            [
                'id' => '1',
                'company_id' => '1',
                'nombre' => 'CARGA INICIAL EMPRESA',
                'descripcion' => 'Carga inicial empresa',
                'nombre_corto' => 'CAREM',
                'afecta' => 'E',
                'accion' => 'S',
                'status' => 1,
            ],
            [
                'id' => '2',
                'company_id' => '1',
                'nombre' => 'CARGA INICIAL CLIENTES',
                'descripcion' => 'Carga inicial clientes',
                'nombre_corto' => 'CARCLI',
                'afecta' => 'E',
                'accion' => 'S',
                'status' => '1',
            ],
            [
                'id' => '3',
                'company_id' => '1',
                'nombre' => 'TRANSFERENCIA ENVIA',
                'descripcion' => 'Transferencia entre bovedas',
                'nombre_corto' => 'TRANENV',
                'afecta' => 'E',
                'accion' => 'R',
                'status' => '1',
            ],
            [
                'id' => '4',
                'company_id' => '1',
                'nombre' => 'TRANSFERENCIA RECIBE',
                'descripcion' => 'Transferencia recibe boveda',
                'nombre_corto' => 'TRANRECI',
                'afecta' => 'E',
                'accion' => 'S',
                'status' => '1',
            ],
        ]);
    }

    public function down()
    {
    }
}
