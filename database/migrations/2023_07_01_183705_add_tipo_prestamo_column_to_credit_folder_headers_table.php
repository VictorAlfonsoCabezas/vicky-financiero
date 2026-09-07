<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPrestamoColumnToCreditFolderHeadersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->integer('tipo_prestamo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('credit_folder_headers', function (Blueprint $table) {
            $table->dropColumn('tipo_prestamo');
        });
    }

}
