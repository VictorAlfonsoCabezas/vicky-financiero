<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacturaColumnsToProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->enum('tipo_factura', ['fisica', 'electronica'])
                ->nullable()
                ->after('status');

            $table->string('numero_autorizacion', 49)
                ->nullable()
                ->after('tipo_factura');

            $table->date('fecha_caducidad')
                ->nullable()
                ->after('numero_autorizacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_factura',
                'numero_autorizacion',
                'fecha_caducidad',
            ]);
        });
    }
}
