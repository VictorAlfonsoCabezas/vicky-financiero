<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNumeroDiasInteresToIntegerInCompanies extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('company', function (Blueprint $table) {
             $table->integer('numero_dias_interes')->nullable()->change(); // Cambia el tipo de dato
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('company', function (Blueprint $table) {
            $table->string('numero_dias_interes')->nullable()->change(); // Cambia el tipo de dato de vuelta a VARCHAR si es necesario
        });
    }

}
