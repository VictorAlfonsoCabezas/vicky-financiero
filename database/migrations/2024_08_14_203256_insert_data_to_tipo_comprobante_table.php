<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDataToTipoComprobanteTable extends Migration
{
    public function up()
    {
        Schema::table('tipo_comprobante', function (Blueprint $table) {
            DB::table('tipo_comprobante')->insert([
                [
                    'id' => '1',
                    'codigo' => '01',
                    'nombre' => strtoupper('Factura'),
                    'status' => true,
                ],
                [
                    'id' => '2',
                    'codigo' => '02',
                    'nombre' => strtoupper('Nota o boleta de venta '),
                    'status' => true,
                ],
                [
                    'id' => '3',
                    'codigo' => '03',
                    'nombre' => strtoupper('Liquidación de compra de Bienes o Prestación de servicios '),
                    'status' => true,
                ],
                [
                    'id' => '4',
                    'codigo' => '04',
                    'nombre' => strtoupper('Nota de crédito'),
                    'status' => true,
                ],
                [
                    'id' => '5',
                    'codigo' => '05',
                    'nombre' => strtoupper('Nota de débito'),
                    'status' => true,
                ],
                [
                    'id' => '6',
                    'codigo' => '09',
                    'nombre' => strtoupper('Tiquetes o vales emitidos por máquinas registradoras'),
                    'status' => true,
                ],
                [
                    'id' => '7',
                    'codigo' => '11',
                    'nombre' => strtoupper('Pasajes expedidos por empresas de aviación'),
                    'status' => true,
                ],
                [
                    'id' => '8',
                    'codigo' => '12',
                    'nombre' => strtoupper('Documentos emitidos por instituciones financieras'),
                    'status' => true,
                ],
                [
                    'id' => '9',
                    'codigo' => '15',
                    'nombre' => strtoupper('Comprobante de venta emitido en el Exterior'),
                    'status' => true,
                ],
                [
                    'id' => '10',
                    'codigo' => '19',
                    'nombre' => strtoupper('Comprobantes de Pago de Cuotas o Aportes'),
                    'status' => true,
                ],
                [
                    'id' => '11',
                    'codigo' => '20',
                    'nombre' => strtoupper('Documentos por Servicios Administrativos emitidos por Inst. del Estado'),
                    'status' => true,
                ],
                [
                    'id' => '12',
                    'codigo' => '21',
                    'nombre' => strtoupper('Carta de Porte Aéreo'),
                    'status' => true,
                ],
                [
                    'id' => '13',
                    'codigo' => '41',
                    'nombre' => strtoupper('Comprobante de venta emitido por reembolso'),
                    'status' => true,
                ],
                [
                    'id' => '14',
                    'codigo' => '42',
                    'nombre' => strtoupper('Documento agente de retención Presuntiva'),
                    'status' => true,
                ],
                [
                    'id' => '15',
                    'codigo' => '43',
                    'nombre' => strtoupper('Liquidación para Explotación y Exploracion de Hidrocarburos'),
                    'status' => true,
                ],
                [
                    'id' => '16',
                    'codigo' => '45',
                    'nombre' => strtoupper('Liquidación por reclamos de aseguradoras'),
                    'status' => true,
                ],
                [
                    'id' => '17',
                    'codigo' => '47',
                    'nombre' => strtoupper('Nota de Crédito por Reembolso Emitida por Intermediario'),
                    'status' => true,
                ],
                [
                    'id' => '18',
                    'codigo' => '48',
                    'nombre' => strtoupper('Nota de Débito por Reembolso Emitida por Intermediario'),
                    'status' => true,
                ],
                [
                    'id' => '19',
                    'codigo' => '294',
                    'nombre' => strtoupper('Liquidación de compra de Bienes Muebles Usados'),
                    'status' => true,
                ],
                [
                    'id' => '20',
                    'codigo' => '344',
                    'nombre' => strtoupper('Liquidación de compra de vehículos usados '),
                    'status' => true,
                ]
            ]);
        });
    }

    public function down()
    {
        Schema::table('tipo_comprobante', function (Blueprint $table) {
            //
        });
    }
}
