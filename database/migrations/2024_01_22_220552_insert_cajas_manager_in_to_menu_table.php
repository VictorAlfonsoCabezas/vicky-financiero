<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertCajasManagerInToMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu', function (Blueprint $table) {
            DB::table('menu')->insert([
                [
                    'menu_id' => '46',
                    'nombre' => 'Manejo Cajas',
                    'url' => '/cajas-manager',
                    'orden' => '8',
                    'icono' => 'fa-box-open',
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
        Schema::table('menu', function (Blueprint $table) {
            DB::table('menu')->where('nombre', 'Manejo Cajas')->delete();
        });
    }
}
