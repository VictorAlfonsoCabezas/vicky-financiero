<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditFolderDetail extends Model
{

    protected $table = 'credit_folder_details';
    protected $fillable = [
        'numero_cuota',
        'company_id',
        'code_folder_header',
        'date_created',
        'hour_created',
        'date_pay',
        'hour_pay',
        'user_pay_id',
        'date_vencimiento',
        'interes_periodo',
        'interes_mora',
        'capital_amortizado',
        'fondo_desgravamen',
        'valor_cuota',
        'valor_pagado',
        'valor_final',
        'adelanto_prox_cuota',
        'saldo_anterior_cuota',
        'faltante_prox_cuota',
        'faltante_anterior_cuota',
        'saldo_remanente',
        'tipo_pago',
        'obervation_pago',
        'path',
        'status',
        'date_cancel',
        'hour_cancel',
        'notificado',
        'valor_gasto',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function formasPago()
    {
        return $this->hasMany(\App\Models\RegistroFormasPago::class, 'letra_id');
    }

    public function header()
    {
        return $this->belongsTo(CreditFolderHeader::class, 'code_folder_header', 'code');
    }

    public function audits()
    {
        return $this->hasMany(CreditFolderAudit::class, 'credit_folder_detail_id');
    }
    
}
