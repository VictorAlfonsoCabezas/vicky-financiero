<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerParentezcoTable extends Migration
{

    public function up()
    {
        Schema::create('customer_parentezco', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('customer_id');
            $table->integer('parentezco_id');
            $table->integer('country_id');
            $table->integer('tipo_documento_id');
            $table->string('nombres_apellidos', 60);
            $table->string('documento', 15);
            $table->string('celular', 12);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_parentezco');
    }
}
