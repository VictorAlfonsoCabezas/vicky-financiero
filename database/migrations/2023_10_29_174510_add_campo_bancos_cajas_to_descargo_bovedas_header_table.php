<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoBancosCajasToDescargoBovedasHeaderTable extends Migration
{
    public function up()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->decimal('valor', 8, 2)->after('boveda_destino_id')->default(0.00);
            $table->integer('bancos_id')->after('company_id')->nullable();
            $table->integer('cajas_id')->after('bancos_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('descargo_bovedas_header', function (Blueprint $table) {
            $table->dropColumn('valor');
            $table->dropColumn('bancos_id');
            $table->dropColumn('cajas_id');
        });
    }
}
