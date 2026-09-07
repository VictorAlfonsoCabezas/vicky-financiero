<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InssertAprobacionIngresoWebToOperacionesDescargoBovedas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {

            DB::table('operaciones_descargo_bovedas')->insert([
                [
                    'company_id' => '1',
                    'nombre' => 'INGRESO VALOR AUTORIZACION',
                    'descripcion' => 'Ingresa a la boveda el valor aprobado, solicitado por la web',
                    'nombre_corto' => 'INVA',
                    'afecta' => 'E',
                    'accion' => 'S',
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
        Schema::table('operaciones_descargo_bovedas', function (Blueprint $table) {
            DB::table('operaciones_descargo_bovedas')->where('nombre_corto', 'INVA')->delete();
        });
    }
}
