<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cajas extends Model
{

    protected $table = 'cajas';
    protected $fillable = [
        'company_id',
        'code',
        'saldo_cuenta',
        'valor_inicial',
        'date_inicial',
        'hour_inicial',
        'user_inicial_id',
        'user_name_inicial',
        'numero_movimientos',
        'numero_movimientos_final',
        'valor_ingreso_clientes',
        'valor_ingreso_clientes_final',
        'valor_egreso_clientes',
        'valor_egreso_clientes_final',
        'valor_ingreso_cajas',
        'valor_ingreso_cajas_final',
        'valor_egreso_cajas',
        'valor_egreso_cajas_final',
        'total',
        'total_final',
        'date_finish',
        'hour_finish',
        'user_finish_id',
        'user_finish_name',
        'cierre',
        'type_cierre',
        'cierre_padre',
        'descuadre',
        'customer_movimiento_id',
        'observacion_cierre',
        'valores_otros',
        'status',
        'user_update_id',
        'user_update_name',
        'date_update',
        'hour_update',
        'descripcion_update',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }
}
