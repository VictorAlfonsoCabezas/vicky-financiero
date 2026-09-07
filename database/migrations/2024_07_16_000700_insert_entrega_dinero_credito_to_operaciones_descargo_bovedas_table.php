<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InsertEntregaDineroCreditoToOperacionesDescargoBovedasTable extends Migration
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
                'id' => '10',
                'company_id' => '1',
                'nombre' => 'ENTREGA DE VALORES DE CREDITO',
                'descripcion' => 'ENTREGA DE VALORES DE CREDITO',
                'nombre_corto' => 'EVC',
                'afecta' => 'E',
                'accion' => 'R',
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
            DB::table('operaciones_descargo_bovedas')->where('nombre_corto', 'EVC')->delete();
            
        });
    }
}
