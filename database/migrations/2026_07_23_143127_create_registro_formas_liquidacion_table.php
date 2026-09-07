<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroFormasLiquidacionTable extends Migration
{
    public function up()
    {
        Schema::create('registro_formas_liquidacion', function (Blueprint $table) {

            $table->bigIncrements('id');

            // Información general
            $table->integer('company_id')->nullable();
            $table->integer('customer_movimientos_id')->nullable();
            $table->integer('customer_id')->nullable();

            // Relación con la liquidación
            $table->unsignedBigInteger('liquidacion_id')->nullable();
            $table->boolean('liquidacion')->default(1);

            // Información de la forma de pago
            $table->string('forma_pago')->nullable();
            $table->integer('forma_pago_id')->nullable();
            $table->integer('banco_id')->nullable();
            $table->decimal('valor', 8, 2)->default(0.00);

            // Información del comprobante
            $table->string('numero_comprobante')->nullable();
            $table->date('fecha_comprobante')->nullable();
            $table->time('hora_comprobante')->nullable();

            // Información de creación
            $table->date('date_create')->nullable();
            $table->time('hour_create')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();

            // Estado de la solicitud
            $table->boolean('status')->default(1);
            $table->boolean('solicitado')->default(0);

            // Información de aprobación/rechazo
            $table->string('usuario_solicitud')->nullable();
            $table->unsignedBigInteger('usuario_id_solicitud')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->time('hora_solicitud')->nullable();

            // Observación
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registro_formas_liquidacion');
    }
}