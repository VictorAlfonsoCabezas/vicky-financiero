<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Gastos extends Model
{
    protected $table = 'gastos';
    protected $fillable = [
        'id',
        'company_id',
        'gastos_categorias_id',
        'sustento_tributario_id',
        'tipo_comprobante_id',
        'numero',
        'establecimiento',
        'punto_emision',
        'autorizacion',
        'fecha_autorizacion',
        'tiene_impuestos',
        'descuento',
        'subtotal_descuento',
        'subtotal_exento',
        'suma_subtotal',
        'suma_iva',
        'total',
        'deducible',
        'proveedor_id',
        'descripcion',
        'fecha_creacion',
        'fecha_aprobacion',
        'valor',
        'user_responsable_id',
        'user_aprueba_id',
        'user_rechaza_id',
        'razon_rechaza',
        'pagado_compania_empleado',
        'estado',
    ];
    protected $casts = [
        'valor' => 'decimal:2',
    ];
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function gastosCategorias()
    {
        return $this->belongsTo('App\Models\GastosCategorias', 'gastos_categorias_id');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Models\Proveedores', 'proveedor_id');
    }

    public function userResponsable()
    {
        return $this->belongsTo('App\User', 'user_responsable_id');
    }

    public function userAprueba()
    {
        return $this->belongsTo('App\User', 'user_aprueba_id');
    }

    public function userRechaza()
    {
        return $this->belongsTo('App\User', 'user_rechaza_id');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // ... code here
        });

        self::created(function ($model) {
            if (self::debeGenerarAsiento($model)) {
                AsientosHeader::recalcularAsientos($model->id, 'gastos');
            }
        });

        self::updating(function ($model) {
            // ... code here
        });

        self::updated(function ($model) {
            if (self::debeGenerarAsiento($model)) {
                AsientosHeader::recalcularAsientos($model->id, 'gastos');
            } elseif ($model->wasChanged('estado')) {
                AsientosHeader::borrarAsientos($model->id, 'gastos');
            }
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            AsientosHeader::borrarAsientos($model->id, 'gastos');
        });
    }

    private static function debeGenerarAsiento($gasto)
    {
        return in_array($gasto->estado, ['APROBADO', 'PAGADO'], true)
            && !empty($gasto->user_aprueba_id)
            && !empty($gasto->fecha_emision);
    }
}
