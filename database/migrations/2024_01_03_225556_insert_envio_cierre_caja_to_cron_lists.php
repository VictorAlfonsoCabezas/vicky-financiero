<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertEnvioCierreCajaToCronLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_lists', function (Blueprint $table) {
            DB::table('cron_lists')->insert([
                [
                    'company_id' => '1',
                    'name' => 'ENVIO DE CIERRES DE CAJA',
                    'detalle' => 'SE ENVIA EL CIERRE DE CAJA A LOS USUARIOS ADMISNITRADORES',
                    'cron' => 'envio_cierre_caja',
                    'status' => 1,
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
        Schema::table('cron_lists', function (Blueprint $table) {
            DB::table('cron_lists')->where('cron', 'envio_cierre_caja')->delete();
        });
    }
}
