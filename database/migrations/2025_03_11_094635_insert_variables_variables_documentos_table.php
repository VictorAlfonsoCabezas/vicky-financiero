<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertVariablesVariablesDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variables_documentos', function (Blueprint $table) {
            DB::table('variables_documentos')->insert([
                [
                    'company_id' => 1,
                    'variable' => 'Valor_Credito',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Nombre_Cliente',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Codigo_Socio',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Numero_Pagare',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Nombre_Caja',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Ciudad_Caja',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Valor_Credito_Letras',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Porcentaje_Credito',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Cuotas_Credito',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Firma_Deudor',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Firma_Garante',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Fecha_Momento',
                ],
                [
                    'company_id' => 1,
                    'variable' => 'Firma_Deudor_Simple',
                ],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
