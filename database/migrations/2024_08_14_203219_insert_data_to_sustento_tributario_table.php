<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataToSustentoTributarioTable extends Migration
{
    public function up()
    {
        Schema::table('sustento_tributario', function (Blueprint $table) {
            DB::table('sustento_tributario')->insert([
                [
                    'id' => '1',
                    'codigo_tributario' => '00',
                    'nombre' => strtoupper('Casos especiales cuyo sustento no aplica en las opciones anteriores'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '2',
                    'codigo_tributario' => '01',
                    'nombre' => strtoupper('Crédito Tributario para declaración de IVA Bienes y Servicios'),
                    'credito_tributario' => true,
                    'status' => true,
                ],
                [
                    'id' => '3',
                    'codigo_tributario' => '02',
                    'nombre' => strtoupper('Costo o Gasto para declaración de IR (sin Crédito Tributario)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '4',
                    'codigo_tributario' => '03',
                    'nombre' => strtoupper('Activo Fijo (Crédito Tributario para declaración de IVA)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '5',
                    'codigo_tributario' => '04',
                    'nombre' => strtoupper('Activo Fijo (Costo o Gasto para declaración de IR - sin Crédito Tributario)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '6',
                    'codigo_tributario' => '05',
                    'nombre' => strtoupper('Liquidación Gastos de Viaje, hospedaje y alimentación Gastos IR (sin Crédito Tributario)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '7',
                    'codigo_tributario' => '06',
                    'nombre' => strtoupper('Inventario (Crédito Tributario para declaración de IVA)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '8',
                    'codigo_tributario' => '07',
                    'nombre' => strtoupper('Inventario (Costo o Gasto para declaración de IR - sin Crédito Tributario)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '9',
                    'codigo_tributario' => '08',
                    'nombre' => strtoupper('Valor pagado para solicitar Reembolso de Gasto (intermediario)'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '10',
                    'codigo_tributario' => '09',
                    'nombre' => strtoupper('Reembolso por Siniestros'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '11',
                    'codigo_tributario' => '10',
                    'nombre' => strtoupper('Distribución de Dividendos, Beneficios o Utilidades'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '12',
                    'codigo_tributario' => '11',
                    'nombre' => strtoupper('Convenios de débito o recaudación para IFI´s'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '13',
                    'codigo_tributario' => '12',
                    'nombre' => strtoupper('Impuestos y retenciones presuntivos'),
                    'credito_tributario' => false,
                    'status' => true,
                ],
                [
                    'id' => '14',
                    'codigo_tributario' => '13',
                    'nombre' => strtoupper('Valores reconocidos por entidades del sector público a favor de sujetos pasivos'),
                    'credito_tributario' => false,
                    'status' => true,
                ]
            ]);
        });
    }

    public function down()
    {
        Schema::table('sustento_tributario', function (Blueprint $table) {
            //
        });
    }
}
