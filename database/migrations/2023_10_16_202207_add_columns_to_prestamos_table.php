<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPrestamosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->integer('edad_maxima')->default(0)->after('tipo');
            $table->integer('edad_minima')->default(0)->after('tipo');
            $table->boolean('diario')->default(0)->after('tipo');
            $table->integer('periodo_id')->nullable()->after('tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropColumn(['edad_maxima', 'edad_minima', 'diario', 'periodo_id']);
        });
    }
}
