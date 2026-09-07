<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bancos;


class RegistroFormasLiquidacion extends Model
{
    protected $table = 'registro_formas_liquidacion';

    protected $fillable = [
        'company_id',
        'customer_movimientos_id',
        'customer_id',
        'liquidacion_id',
        'liquidacion',
        'forma_pago',
        'forma_pago_id',
        'banco_id',
        'valor',
        'numero_comprobante',
        'fecha_comprobante',
        'hora_comprobante',
        'date_create',
        'hour_create',
        'user_id',
        'user_name',
        'status',
        'solicitado',
        'usuario_solicitud',
        'usuario_id_solicitud',
        'fecha_solicitud',
        'hora_solicitud',
        'observacion',
    ];

    public function banco()
    {
        return $this->belongsTo(Bancos::class, 'banco_id');
    }
}