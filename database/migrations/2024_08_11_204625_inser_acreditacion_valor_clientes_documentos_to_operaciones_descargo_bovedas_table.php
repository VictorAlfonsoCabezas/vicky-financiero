<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InserAcreditacionValorClientesDocumentosToOperacionesDescargoBovedasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('operaciones_descargo_bovedas')->insert([
            [
           
                'company_id' => '1',
                'nombre' => 'ACREDITACION DE VALORES POR CARGA MASIVA DE VALORES A CUENTAS',
                'descripcion' => 'ACREDITACION DE VALORES POR CARGA MASIVA DE VALORES A CUENTAS',
                'nombre_corto' => 'AVCMC',
                'afecta' => 'E',
                'accion' => 'S',
                'status' => '1',
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {
            DB::table('operaciones_descargo_bovedas')->where('nombre_corto', 'AVCMC')->delete();
        });
    }
}
