<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertGenerarCategoriasInToGastosCategoriasTable extends Migration
{
    public function up()
    {
        Schema::table('gastos_categorias', function (Blueprint $table) {
            DB::table('gastos_categorias')->insert([
                [
                    'company_id' => '1',
                    'nombre' => 'ALQUILER DE OFICINAS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'MANTENIMIENTO DE INSTALACIONES',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'ELECTRICIDAD',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'AGUA',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'GAS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'INTERNET',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'TELÉFONO',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'PAPELERIA',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'MATERIAL LIMPIEZA',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'MATERIAL OFICINA',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'SALARIOS Y SUELDOS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'BONIFICACIONES',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'SEGURIDAD SOCIAL',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'CAPACITACIONES Y DESARROLLO',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'COMBUSTIBLE',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'SOFTWARE',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'HARDWARE',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'SERVICIOS DE TI',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'MANTENIMIENTO DE SISTEMAS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'COMBUSTIBLE',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'MANTENIMIENTO DE VEHÍCULOS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'TRANSPORTE PÚBLICO',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'ALQUILER DE VEHÍCULOS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'PUBLICIDAD',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'PROMOCIONES',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'MATERIAL PROMOCIONAL',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'EVENTOS Y FERIAS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'HONORIARIOS PROFESIONALES',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'HONORIARIOS CONSULTORÍAS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'SERVICIOS LEGALES',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'AUDITORIAS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'IMPREVISTOS',
                    'status' => 1,
                ],
                [
                    'company_id' => '1',
                    'nombre' => 'ALIMENTACION',
                    'status' => 1,
                ],
            ]);
        });
    }

    public function down()
    {
        Schema::table('gastos_categorias', function (Blueprint $table) {
            //
        });
    }
}
