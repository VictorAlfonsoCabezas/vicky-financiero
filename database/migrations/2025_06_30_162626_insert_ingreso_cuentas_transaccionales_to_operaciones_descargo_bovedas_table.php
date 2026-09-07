<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertIngresoCuentasTransaccionalesToOperacionesDescargoBovedasTable extends Migration
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
                  
                    'company_id' => 1,
                    'nombre' => 'INGRESO CUENTAS TRANSACCIONALES',
                    'descripcion' => 'Se ingresan valores de cuentas transaccionales',
                    'nombre_corto' => 'ICT',
                    'afecta' => 'E',
                    'accion' => 'S',                   
                ]
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
            DB::table('operaciones_descargo_bovedas')->where('nombre_corto', 'ICT')->delete();
        });
    }
}
