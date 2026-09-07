<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DescargoBovedasHeader extends Model
{
    protected $table = 'descargo_bovedas_header';
    protected $fillable = [
        'id',
        'company_id',
        'bancos_id',
        'cajas_id',
        'credit_folder_header_id',
        'customer_movimiento_id',
        'customer_movimiento_solicitud_id',
        'operaciones_descargo_bovedas_id',
        'boveda_origen_id',
        'boveda_destino_id',
        'valor',
        'observacion',
        'fecha_creacion',
        'estado',
        'status',
        'cuenta_origen',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            self::generarAsientoAutomatico($model);
        });

        self::updated(function ($model) {
            if (
                $model->wasChanged('valor') ||
                $model->wasChanged('operaciones_descargo_bovedas_id') ||
                $model->wasChanged('estado') ||
                $model->wasChanged('status')
            ) {
                self::generarAsientoAutomatico($model);
            }
        });

        self::deleted(function ($model) {
            if (Auth::check()) {
                $asiento = AsientosHeader::where('company_id', Auth::user()->company_id)
                    ->where('descargo_bovedas_header_id', $model->id)
                    ->first();

                if ($asiento) {
                    AsientosDetalle::where('company_id', Auth::user()->company_id)
                        ->where('asientos_header_id', $asiento->id)
                        ->delete();
                    $asiento->delete();
                }
            }
        });
    }

    private static function generarAsientoAutomatico($model)
    {
        if (!Auth::check()) {
            return;
        }

        if (in_array($model->status, [false, 0, '0'], true) || $model->estado !== 'FINALIZADO') {
            self::borrarAsientoAutomatico($model);
            return;
        }

        $company = Company::find($model->company_id);
        if ($company && $company->puedeContabilizar($model->fecha_creacion)) {
            AsientosHeader::recalcularAsientosBovedas($model->id);
        }
    }

    private static function borrarAsientoAutomatico($model)
    {
        $asiento = AsientosHeader::where('company_id', $model->company_id)
            ->where('descargo_bovedas_header_id', $model->id)
            ->first();

        if (!$asiento) {
            return;
        }

        AsientosDetalle::where('company_id', $model->company_id)
            ->where('asientos_header_id', $asiento->id)
            ->delete();

        $asiento->delete();
    }
}
