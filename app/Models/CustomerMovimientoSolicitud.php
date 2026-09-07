<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMovimientoSolicitud extends Model
{
    protected $table = 'customer_movimiento_solicitud';
    protected $fillable = [
        'id',
        'company_id',
        'customer_id',
        'customer_tipo_ahorro_id',
        'banco_id',
        'comprobante',
        'numero_deposito',
        'type_transaction_id',
        'forma_pago_id',
        'customer_movimiento_id',
        'user_id',
        'fecha_creacion',
        'customer_tipo_ahorro_id',
        'valor',
        'observacion',
        'estado',
        'fecha_rechazado',
        'razon_rechazado',
        'user_rechazado',
        'path',
        'archivo',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function cuenta()
    {
        return $this->belongsTo(CustomerTipoAhorros::class, 'customer_tipo_ahorro_id');
    }

    public function banco()
    {
        return $this->belongsTo(Bancos::class, 'banco_id');
    }

    public function customerTipoAhorro()
    {
        return $this->belongsTo('App\Models\CustomerTipoAhorros', 'customer_tipo_ahorro_id');
    }
}
