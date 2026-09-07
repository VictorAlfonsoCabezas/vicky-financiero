<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsientosDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('asientos_detalle', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('asientos_header_id');
            $table->integer('plan_cuentas_id');
            $table->decimal('valor', 8, 2)->default(0.00);
            $table->boolean('debe_haber')->default(true);
            $table->datetime('fecha_creacion');
            $table->integer('user_created');
            $table->string('observacion')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asientos_detalle');
    }
}
