<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroComprobanteToCreditFolderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_folder_details', function (Blueprint $table) {

            // Banco donde se hizo la transferencia
            $table->unsignedBigInteger('banco_id')->nullable()->after('tipo_pago');

            // Número de comprobante
            $table->string('numero_comprobante')->nullable()->after('banco_id');

            // Foreign key
            $table->foreign('banco_id')
                ->references('id')
                ->on('bancos')
                ->onDelete('set null');

            // Unique compuesto profesional
            $table->unique(
                ['numero_comprobante', 'banco_id', 'company_id'],
                'unique_comprobante_banco_company'
            );
        });
    }

    public function down()
    {
        Schema::table('credit_folder_details', function (Blueprint $table) {

            $table->dropUnique('unique_comprobante_banco_company');
            $table->dropForeign(['banco_id']);
            $table->dropColumn(['banco_id', 'numero_comprobante']);
        });
    }
}
