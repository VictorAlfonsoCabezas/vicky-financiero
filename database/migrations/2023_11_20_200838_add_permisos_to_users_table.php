<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermisosToUsersTable extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('permiso_credito_aprobar')->after('admin')->default(0);
            $table->boolean('permiso_caja_valor')->after('admin')->default(0);
        });
    }


    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permiso_credito_aprobar');
            $table->dropColumn('permiso_caja_valor');
        });
    }
}
