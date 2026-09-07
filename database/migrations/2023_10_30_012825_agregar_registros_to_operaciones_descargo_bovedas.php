<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AgregarRegistrosToOperacionesDescargoBovedas extends Migration {

    public function up() {
        DB::table('operaciones_descargo_bovedas')->insert([
            [
                'id' => '5',
                'company_id' => '1',
                'nombre' => 'CAJA INICIA',
                'descripcion' => 'CAJA INICIA',
                'nombre_corto' => 'CAJINI',
                'afecta' => 'E',
                'accion' => 'R',
                'status' => '1',
            ],
            [
                'id' => '6',
                'company_id' => '1',
                'nombre' => 'CAJA CIERRE',
                'descripcion' => 'CAJA CIERRE',
                'nombre_corto' => 'CAJCIE',
                'afecta' => 'E',
                'accion' => 'S',
                'status' => '1',
            ],
            [
                'id' => '7',
                'company_id' => '1',
                'nombre' => 'DESCARGO DE CAJA',
                'descripcion' => 'DESCARGO DE CAJA',
                'nombre_corto' => 'CAJDESC',
                'afecta' => 'E',
                'accion' => 'R',
                'status' => '1',
            ],
        ]);
    }

}
