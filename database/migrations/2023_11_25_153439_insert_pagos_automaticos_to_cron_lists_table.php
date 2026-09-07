<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertPagosAutomaticosToCronListsTable extends Migration
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
                    'name' => 'DEBITO AUTOMATICO',
                    'detalle' => 'HACE EL PAGO DE LOS CREDITOS AUTOMATICAMENTE',
                    'cron' => 'pagos_automaticos',
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
            DB::table('cron_lists')->where('name_corto', 'SOLICITUD DE ACREDITACION EN CUENTA')->delete();
        });
    }
}
