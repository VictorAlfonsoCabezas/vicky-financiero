<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditFolderAuditsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_folder_audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('credit_folder_header_id')->nullable();
            $table->unsignedBigInteger('credit_folder_detail_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('code_folder_header', 100)->nullable();
            $table->string('source', 20);
            $table->string('event', 20);
            $table->string('action', 50)->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();

            // No se usan llaves foraneas: la trazabilidad debe sobrevivir
            // a la eliminacion fisica de cabeceras, detalles y usuarios.
            $table->index(
                ['credit_folder_header_id', 'created_at'],
                'credit_audit_header_date_index'
            );
            $table->index(
                ['credit_folder_detail_id', 'created_at'],
                'credit_audit_detail_date_index'
            );
            $table->index(
                ['company_id', 'code_folder_header'],
                'credit_audit_company_code_index'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_folder_audits');
    }
}
