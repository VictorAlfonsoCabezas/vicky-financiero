<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InsertOperacionIngresoEncajeToOperacionesDescargoBovedasTable extends Migration
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
                'nombre' => 'INGRESO DE VALORES DE RETENCION DE ENCAJES',
                'descripcion' => 'INGRESO DE VALORES DE RETENCION DE ENCAJES',
                'nombre_corto' => 'IVRE',
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
            DB::table('operaciones_descargo_bovedas')->where('nombre_corto', 'IVRE')->delete();
            
        });
    }
}
