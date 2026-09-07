<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInteresAnualColumnToPrestamosTable extends Migration {

    public function up() {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->decimal('interes_anual', 8, 2)->after('interes')->default(0.00);
        });
    }

    public function down() {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropColumn('interes_anual');
        });
    }

}
