<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditFolderHeader extends Model
{

    protected $table = 'credit_folder_headers';
    protected $fillable = [
        'company_id',
        'code',
        'customer_id',
        'customer_code',
        'customer_ruc',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_email',
        'customer_garante_id',
        'customer_garante_name',
        'valor_solicitado',
        'suma_valores_gastos_prestamo',
        'total_pagando',
        'anios_pagar',
        'cuotas_pagar',
        'valor_cuota',
        'valor_desgravamen',
        'valor_interes_pago',
        'tipo_pago',
        'date_created',
        'hour_created',
        'user_created_id',
        'user_created_name',
        'date_verified',
        'hour_verified',
        'user_verified_id',
        'user_verified_name',
        'valor_encaje',
        'path_encaje',
        'date_cancel',
        'hour_cancel',
        'user_cancel',
        'user_cancel_id',
        'user_cancel_name',
        'status',
        'forma_pago_id',
        'documento_desembolso',
        'tipo_prestamo',
        'valor_liquidar',
        'valor_novacion',
        'administrativo_porcentaje_valor',
        'gasto_administrativo',
        'encaje',
        'encaje_credito_cuenta',
        'encaje_porcentaje_valor',
        'encaje_cantidad',
        'encaje_valor',
        'primer_gasto',
        'segundo_gasto',
        'tercer_gasto',
        'porcentaje_primer_gasto',
        'porcentaje_segundo_gasto',
        'porcentaje_tercer_gasto',
        'ahorro',
        'porcentaje_interes_prestamo',
        'cuenta_contable_id',

    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function cuentaContable()
    {
        return $this->belongsTo('App\Models\PlanCuentas', 'cuenta_contable_id');
    }

    public function details()
    {
        return $this->hasMany(CreditFolderDetail::class, 'code_folder_header', 'code');
    }

    public function audits()
    {
        return $this->hasMany(CreditFolderAudit::class, 'credit_folder_header_id');
    }
}
