<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAccionesDetalleTable extends Migration
{
    
    public function up()
    {
        Schema::table('acciones_detalle', function (Blueprint $table) {
            $table->integer('user_entrega_id')->nullable()->after('status'); 
            $table->date('fecha_entrega')->nullable()->after('status'); 
            $table->time('hora_entrega')->nullable()->after('status'); 
        });
    }

 
    public function down()
    {
        Schema::table('acciones_detalle', function (Blueprint $table) {
            $table->dropColumn('user_entrega_id');
            $table->dropColumn('fecha_entrega');
            $table->dropColumn('hora_entrega');
        });
    }
}
