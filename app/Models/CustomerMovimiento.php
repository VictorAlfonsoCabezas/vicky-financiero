<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerMovimiento extends Model
{

    protected $table = 'customer_movimientos';
    protected $fillable = [
        'id',
        'code',
        'comprobante',
        'company_id',
        'customer_id',
        'customer_code',
        'afecta',
        'customer_name',
        'customer_ruc',
        'customer_address',
        'customer_telefono',
        'customer_tipo_ahorro_id',
        'tipo_ahorro_detalle_id',
        'type_transaction_id',
        'type_transaction_name',
        'type_transaction_action',
        'banco_id', 
        'numero_deposito',
        'forma_pago_id', 
        'forma_pago_name',
        'valor_movimiento',
        'saldo_general',
        'credit_folder_header_id',
        'credit_folder_details_id',
        'gastos_id',
        'gastos_forma_pagos_id',
        'observation',
        'user_created_id',
        'user_created_name',
        'date_created',
        'hour_created',
        'user_cancel_id',
        'user_cancel_name',
        'date_cancel',
        'hour_cancel',
        'razon_cancel',
        'automatico',
        'status',
        'plazo_dias_programado',
        'tipo_ahorros_programados_detalle_id',
        'valor_calculadora',
        'status_programado',
        'carga_masiva',
        'id_forma_Pago',
        'gastos_plan_cuentas_id',
        'gastos_centro_costos_id',
    ];

    public function userCreated()
    {
        return $this->belongsTo('app\User', 'user_created_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function customerTipoAhorro()
    {
        return $this->belongsTo('App\Models\CustomerTipoAhorros', 'customer_tipo_ahorro_id');
    }

    public function CreditFolderHeader()
    {
        return $this->belongsTo('App\Models\CreditFolderDetail', 'credit_folder_header_id');
    }

    public function creditFolderDetails()
    {
        return $this->belongsTo('App\Models\CreditFolderDetail', 'credit_folder_details_id');
    }

    public function typeTransaction()
    {
        return $this->belongsTo('App\Models\TypeTransaction', 'type_transaction_id');
    }

    public function userCancel()
    {
        return $this->belongsTo('app\User', 'user_cancel_id');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // ... code here
        });

        self::created(function ($model) {
            self::generarAsientoAutomatico($model);
        });

        self::updating(function ($model) {
            // ... code here
        });

        self::updated(function ($model) {
            if (
                self::estaActivoParaContabilidad($model)
                && ($model->wasChanged('type_transaction_id') || $model->wasChanged('valor_movimiento') || $model->wasChanged('banco_id'))
            ) {
                self::generarAsientoAutomatico($model);
            }
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            AsientosHeader::borrarAsientos($model->id, 'customer_movimientos');
        });
    }

    private static function generarAsientoAutomatico($model)
    {
        if (!self::estaActivoParaContabilidad($model)) {
            return;
        }

        if (!Auth::check() && $model->user_created_id) {
            Auth::loginUsingId($model->user_created_id);
        }

        if (Auth::check()) {
            AsientosHeader::recalcularAsientos($model->id, 'customer_movimientos');
        }
    }

    private static function estaActivoParaContabilidad($model)
    {
        return !in_array($model->status, [false, 0, '0'], true);
    }
}
