<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Log;

class AsientosHeader extends Model
{
    protected $table = 'asientos_header';
    protected $fillable = [
        'company_id',
        'manual',
        'fecha_contable',
        'fecha_creacion',
        'user_created',
        'concepto_id',
        'config_plan_header_id',
        'descripcion',
        'customer_movimiento_id',
        'gasto_id',
        'descargo_bovedas_header_id',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function detalles()
    {
        return $this->hasMany(AsientosDetalle::class, 'asientos_header_id', 'id')
            ->orderBy('debe_haber', 'DESC')
            ->orderBy('id');
    }

    public function concepto()
    {
        return $this->belongsTo('App\Models\Conceptos', 'concepto_id');
    }

    public function customerMovimiento()
    {
        return $this->belongsTo('App\Models\CustomerMovimiento', 'customer_movimiento_id');
    }

    public function configPlanHeader()
    {
        return $this->belongsTo('App\Models\ConfigPlanHeader', 'config_plan_header_id');
    }

    public function getNombreTransaccionAttribute()
    {
        if ($this->config_plan_header_id) {
            return optional(optional($this->configPlanHeader)->typeTransaction)->name ?? 'Asiento General';
        }
        if ($this->customer_movimiento_id) {
            return optional(optional($this->customerMovimiento)->typeTransaction)->name ?? 'Movimiento Cliente';
        }
        return 'FACTURA DE PROVEEDORES';
    }

    public static function recalcularAsientos($id, $table = 'customer_movimientos', $general = false, $typeTransaction = null)
    {
        // Mensaje por defecto
        $data = [
            'code' => '202',
            'msg' => 'No se recalcula, configurar periodos contables y fechas de inicio de contabilidad'
        ];

        $company = Company::find(Auth::user()->company_id);
        switch ($table) {
            case 'customer_movimientos':
                $movimientos = CustomerMovimiento::find($id);
                if (!$movimientos && !$general) {
                    return [
                        'code' => '404',
                        'msg' => 'No existe el movimiento para recalcular el asiento.'
                    ];
                }

                $transaction = $typeTransaction ?? TypeTransaction::find($movimientos->type_transaction_id);
                if (!$transaction) {
                    return [
                        'code' => '201',
                        'msg' => 'No existe el tipo de transaccion para recalcular el asiento.'
                    ];
                }

                $fechaContable = $general ? date('Y-m-d') : $movimientos->date_created;
                break;
            case 'gastos':
                $transaction = TypeTransaction::where('name_corto', 'GAS')->first();
                $movimientos = Gastos::find($id);
                if (!$movimientos) {
                    return [
                        'code' => '404',
                        'msg' => 'No existe el gasto para recalcular el asiento.'
                    ];
                }

                if (!$transaction) {
                    return [
                        'code' => '201',
                        'msg' => 'No existe el tipo de transaccion GAS para recalcular el asiento.'
                    ];
                }

                $fechaGasto = $movimientos->fecha_emision ?: $movimientos->fecha_aprobacion;
                $fechaContable = date('Y-m-d', strtotime($fechaGasto));
                break;
        }
        if ($company && ($general ? $company->puedeContabilizar() : $company->puedeContabilizar($fechaContable))) {
            $idDiario = self::obtenerConceptoIdPorNombre('DIARIO');
            $idEgreso = self::obtenerConceptoIdPorNombre('EGRESO');
            if (!$idDiario || !$idEgreso) {
                $data = [
                    'code' => '201',
                    'msg' => 'No existe concepto DIARIO o EGRESO en la configuración'
                ];
                return $data;
            }
            $queryHeader = ConfigPlanHeader::where('company_id', Auth::user()->company_id)
                ->where('type_transaction_id', $transaction->id)
                ->where('status', true);
            if ($transaction->name_corto === 'EUS') {
                $queryHeader->where('concepto_id', $idEgreso);
            }
            $configHeader = $queryHeader->first();

            $configHeaderGeneral = ConfigPlanHeader::where('company_id', Auth::user()->company_id)
                ->where('type_transaction_id', $transaction->id)
                ->where('status', 1)
                ->where('concepto_id', $idDiario)
                ->first();


            if (!$configHeader || ($general && !$configHeaderGeneral)) {
                $data = [
                    'code' => '201',
                    'msg' => 'No existe configuracion del Plan de ese modulo'
                ];
                return $data;
            }
            if ($general) {
                $configHeader = $configHeaderGeneral;
            }

            $configDetalle = ConfigPlanDetalle::where('company_id', Auth::user()->company_id)
                ->where('config_plan_header_id', $configHeader->id)
                ->where('status', true)
                ->orderBy('debe_haber', 'DESC')
                ->get();

            
            if ($configDetalle->isEmpty()) {
                $data = [
                    'code' => '202',
                    'msg' => 'No existe debe y haber en la configuración'
                ];
                return $data;
            }
            if (!is_null($transaction->name_corto)) {
                switch ($table) {
                    case 'customer_movimientos':
                        //Cabecera de Asiento
                        $agruparPagoCredito = !$general
                            && in_array($transaction->name_corto, ['PC', 'PCA', 'PCI'])
                            && !empty($movimientos->credit_folder_details_id);

                        $movimientosPagoCreditoIds = collect();
                        if ($agruparPagoCredito) {
                            $movimientosPagoCreditoIds = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                                ->where('credit_folder_details_id', $movimientos->credit_folder_details_id)
                                ->where('type_transaction_id', $movimientos->type_transaction_id)
                                ->where(function ($query) {
                                    $query->whereNull('status')
                                        ->orWhereNotIn('status', [false, 0, '0']);
                                })
                                ->orderBy('id')
                                ->pluck('id');

                            $movimientosRegistroPagoIds = RegistroFormasPago::where('company_id', Auth::user()->company_id)
                                ->where('letra_id', $movimientos->credit_folder_details_id)
                                ->whereIn('solicitado', [1, 3])
                                ->where(function ($query) {
                                    $query->whereNull('status')
                                        ->orWhereNotIn('status', [2, false, 0, '0']);
                                })
                                ->pluck('customer_movimientos_id')
                                ->filter()
                                ->unique()
                                ->values();

                            if ($movimientosRegistroPagoIds->isNotEmpty()) {
                                $movimientosPagoCreditoIds = $movimientosRegistroPagoIds;
                            }
                        }

                        $queryAsientoHeader = AsientosHeader::where('company_id', Auth::user()->company_id)
                            ->where('status', true);

                        if ($agruparPagoCredito && $movimientosPagoCreditoIds->isNotEmpty()) {
                            $queryAsientoHeader->whereIn('customer_movimiento_id', $movimientosPagoCreditoIds);
                        } else {
                            $queryAsientoHeader->where('customer_movimiento_id', $id);
                        }

                        $existeAsientoHeader = $queryAsientoHeader->exists();

                        if ($existeAsientoHeader) {
                            $queryAsientoHeader = AsientosHeader::where('company_id', Auth::user()->company_id)
                                ->where('status', true);

                            if ($agruparPagoCredito && $movimientosPagoCreditoIds->isNotEmpty()) {
                                $queryAsientoHeader->whereIn('customer_movimiento_id', $movimientosPagoCreditoIds);
                            } else {
                                $queryAsientoHeader->where('customer_movimiento_id', $id);
                            }

                            $asientosHeader = $queryAsientoHeader->orderBy('id')->first();
                        } elseif ($general) {
                            $asientosHeader = AsientosHeader::where('id', $id)->first();
                        } else {
                            $asientosHeader = new AsientosHeader();
                            $asientosHeader->company_id = Auth::user()->company_id;
                        }
                        $asientosHeader->manual = false;
                        $asientosHeader->fecha_contable = $general ? $asientosHeader->fecha_contable : $movimientos->date_created . ' ' . $movimientos->hour_created;
                        $asientosHeader->fecha_creacion = date('Y-m-d H:i:s');
                        $asientosHeader->user_created = $general ? $asientosHeader->user_created : $movimientos->user_created_id;
                        $asientosHeader->concepto_id = $configHeader->concepto_id;
                        $asientosHeader->config_plan_header_id = $configHeader->id;
                        $asientosHeader->descripcion = 'ASIENTO AUTOMATICO';
                        $asientosHeader->customer_movimiento_id = $general ? null : ($agruparPagoCredito && $movimientosPagoCreditoIds->isNotEmpty() ? $movimientosPagoCreditoIds->first() : $id);
                        $asientosHeader->status = true;

                        // Validar si vienen de otra operacion
                        $asientosHeader->gasto_id = $movimientos->gastos_id ?? null;

                        $asientosHeader->save();

                        if ($agruparPagoCredito && $movimientosPagoCreditoIds->isNotEmpty()) {
                            $headersDuplicados = AsientosHeader::where('company_id', Auth::user()->company_id)
                                ->where('status', true)
                                ->whereIn('customer_movimiento_id', $movimientosPagoCreditoIds)
                                ->where('id', '!=', $asientosHeader->id)
                                ->get();

                            foreach ($headersDuplicados as $headerDuplicado) {
                                AsientosDetalle::where('company_id', Auth::user()->company_id)
                                    ->where('asientos_header_id', $headerDuplicado->id)
                                    ->delete();
                                $headerDuplicado->delete();
                            }
                        }
                        break;
                    case 'gastos':
                        //Cabecera de Asiento
                        $existeAsientoHeader = AsientosHeader::where('company_id', Auth::user()->company_id)
                            ->where('status', true)
                            ->where('gasto_id', $id)
                            ->exists();

                        if ($existeAsientoHeader) {
                            $asientosHeader = AsientosHeader::where('company_id', Auth::user()->company_id)
                                ->where('status', true)
                                ->where('gasto_id', $id)
                                ->first();
                        } else {
                            $asientosHeader = new AsientosHeader();
                            $asientosHeader->company_id = Auth::user()->company_id;
                        }

                        $asientosHeader->manual = false;
                        $asientosHeader->fecha_contable = $fechaContable;
                        $asientosHeader->fecha_creacion = date('Y-m-d H:i:s');
                        $asientosHeader->user_created = $movimientos->user_aprueba_id;
                        $asientosHeader->concepto_id = $configHeader->concepto_id;
                        $asientosHeader->config_plan_header_id = $configHeader->id;
                        $asientosHeader->descripcion = 'ASIENTO AUTOMATICO';
                        $asientosHeader->gasto_id = $id;
                        $asientosHeader->status = true;
                        $asientosHeader->save();
                        break;
                }

                switch ($transaction->name_corto) {
                    case 'GAS': //GASTOS DE CAJA
                        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $asientosHeader->id)->delete();
                        foreach ($configDetalle as $key => $value) {
                            if ($value->debe_haber) {
                                $planes = GastosPlanCuentas::where('gasto_id', $id)->get();
                                foreach ($planes as $pla) {
                                    $asientosDetalle = new AsientosDetalle();
                                    $asientosDetalle->company_id = Auth::user()->company_id;
                                    $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                    $asientosDetalle->plan_cuentas_id = $pla->plan_cuentas_id;
                                    $asientosDetalle->valor = $pla->valor;
                                    $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_aprueba_id);
                                    $asientosDetalle->debe_haber = $value->debe_haber;
                                    $asientosDetalle->fecha_contable = $fechaContable;
                                    $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                    $asientosDetalle->user_created = $movimientos->user_aprueba_id;
                                    $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                    $asientosDetalle->status = true;
                                    $asientosDetalle->save();
                                }
                            } else {
                                $asientosDetalle = new AsientosDetalle();
                                $asientosDetalle->company_id = Auth::user()->company_id;
                                $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                $asientosDetalle->valor = $movimientos->valor;
                                $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_aprueba_id);
                                $asientosDetalle->debe_haber = $value->debe_haber;
                                $asientosDetalle->fecha_contable = $fechaContable;
                                $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                $asientosDetalle->user_created = $movimientos->user_aprueba_id;
                                $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                $asientosDetalle->status = true;
                                $asientosDetalle->save();
                            }
                        }
                        // Recalcular FP
                        $fps = GastosFormaPagos::where('gasto_id', $id)->get();
                        foreach ($fps as $key => $fp) {
                            # code...
                        }
                        break;
                    case 'PPR': //PAGO PROVEEDORES
                        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $asientosHeader->id)->delete();
                        foreach ($configDetalle as $key => $value) {
                            $valorFinal = 0;
                            if ($value->tabla == "" || $value->tabla == null) {
                                $valorFinal = $movimientos->{$value->campo_foraneo};
                            }

                            if ($value->debe_haber) {
                                $asientosDetalle = new AsientosDetalle();
                                $asientosDetalle->company_id = Auth::user()->company_id;
                                $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                $asientosDetalle->valor = $valorFinal;
                                $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_created_id);
                                $asientosDetalle->debe_haber = $value->debe_haber;
                                $asientosDetalle->fecha_contable = $movimientos->date_created . ' ' . $movimientos->hour_created;
                                $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                $asientosDetalle->user_created = $movimientos->user_created_id;
                                $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                $asientosDetalle->status = true;
                                $asientosDetalle->save();
                            } else {
                                $asientosDetalle = new AsientosDetalle();
                                $asientosDetalle->company_id = Auth::user()->company_id;
                                $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                $asientosDetalle->valor = $valorFinal;
                                $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_created_id);
                                $asientosDetalle->debe_haber = $value->debe_haber;
                                $asientosDetalle->fecha_contable = $movimientos->date_created . ' ' . $movimientos->hour_created;
                                $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                $asientosDetalle->user_created = $movimientos->user_created_id;
                                $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                $asientosDetalle->status = true;
                                $asientosDetalle->save();
                            }
                        }
                        break;
                    case 'ENC': //ENTREGA DE CREDITO
                        $prestamo = CreditFolderHeader::find($movimientos->credit_folder_header_id);
                        AsientosDetalle::where('company_id', Auth::user()->company_id)
                            ->where('asientos_header_id', $asientosHeader->id)
                            ->delete();
                        if (!$prestamo) {
                            self::eliminarAsientoTemporal($asientosHeader);

                            return [
                                'code' => '204',
                                'msg' => 'No se genero el asiento: la entrega de credito no tiene cabecera de credito asociada.'
                            ];
                        }

                        if (isset($prestamo->tipo_pago) && $prestamo->tipo_pago == "MENSUAL") {
                            $diasPrestamos = $prestamo->cuotas_pagar * 30;

                            foreach ($configDetalle as $key => $value) {

                                if ($value->tabla == "" || $value->tabla == null) {
                                    $valorFinal = $movimientos->valor_movimiento;
                                } else {
                                    $idPago = (int) $movimientos->{$value->campo_foraneo};
                                    $cuota = CreditFolderDetail::find($idPago);
                                    $valorFinal = $cuota->{$value->campo};
                                }
                                //$planCuentas = PlanCuentas::find($value->plan_cuentas_id);
                                $planCuentas = PlanCuentas::find($value->plan_cuentas_id);
                                if (!$planCuentas) {
                                    self::eliminarAsientoTemporal($asientosHeader);

                                    return [
                                        'code' => '204',
                                        'msg' => 'No se genero el asiento: la configuracion contable ENC apunta a una cuenta inexistente. Plan cuenta ID: ' . $value->plan_cuentas_id
                                    ];
                                }

                                if ((float) $valorFinal <= 0) {
                                    continue;
                                }

                                if ($planCuentas->prestamo) {
                                    //Buscar Plan
                                    $rangoTiempo = false;
                                    if ($diasPrestamos >= $planCuentas->tiempo_inicio && $diasPrestamos <= $planCuentas->tiempo_fin) {
                                        $rangoTiempo = true;
                                    }

                                    if (!$rangoTiempo && is_null($planCuentas->tiempo_fin)) {
                                        if ($diasPrestamos >= $planCuentas->tiempo_inicio) {
                                            $rangoTiempo = true;
                                        }
                                    }

                                    if ($rangoTiempo) {
                                        $asientosDetalle = new AsientosDetalle();
                                        $asientosDetalle->company_id = Auth::user()->company_id;
                                        $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                        $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                        $asientosDetalle->valor = $valorFinal;
                                        $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_created_id);
                                        $asientosDetalle->debe_haber = $value->debe_haber;
                                        $asientosDetalle->fecha_contable = $movimientos->date_created . ' ' . $movimientos->hour_created;
                                        $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                        $asientosDetalle->user_created = $movimientos->user_created_id;
                                        $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                        $asientosDetalle->status = true;
                                        $asientosDetalle->save();
                                    }
                                } else {
                                    if ($movimientos->banco_id == $planCuentas->banco_id) {
                                        $asientosDetalle = new AsientosDetalle();
                                        $asientosDetalle->company_id = Auth::user()->company_id;
                                        $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                        $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                        $asientosDetalle->valor = $valorFinal;
                                        $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_created_id);
                                        $asientosDetalle->debe_haber = $value->debe_haber;
                                        $asientosDetalle->fecha_contable = $movimientos->date_created . ' ' . $movimientos->hour_created;
                                        $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                        $asientosDetalle->user_created = $movimientos->user_created_id;
                                        $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                        $asientosDetalle->status = true;
                                        $asientosDetalle->save();
                                    }
                                }
                            }
                        }
                        if (!isset($prestamo->tipo_pago) || $prestamo->tipo_pago != "MENSUAL") {
                            self::eliminarAsientoTemporal($asientosHeader);

                            return [
                                'code' => '204',
                                'msg' => 'No se genero el asiento: el credito no tiene tipo de pago MENSUAL configurado.'
                            ];
                        }
                        break;
                    case 'PC': //PAGO CREDITOS
                    case 'PCA': //PAGO CREDITOS AUTOMATICO
                    case 'PCI': //PAGO CREDITOS INCOBRABLES
                        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $asientosHeader->id)->delete();
                        $cuotaPago = $movimientos->creditFolderDetails;
                        if (!$cuotaPago) {
                            self::eliminarAsientoTemporal($asientosHeader);

                            return [
                                'code' => '204',
                                'msg' => 'No se genero el asiento: el pago de credito no tiene cuota asociada (credit_folder_details_id).'
                            ];
                        }

                        $prestamo = CreditFolderHeader::where('code', $cuotaPago->code_folder_header)->first();
                        if (!$prestamo) {
                            self::eliminarAsientoTemporal($asientosHeader);

                            return [
                                'code' => '204',
                                'msg' => 'No se genero el asiento: no existe la cabecera del credito asociada a la cuota.'
                            ];
                        }

                        if (isset($prestamo->tipo_pago) && $prestamo->tipo_pago == "MENSUAL") {
                            $diasPrestamos = $prestamo->cuotas_pagar * 30;
                            $valorPagoContable = 0;
                            foreach ($configDetalle as $detallePago) {
                                if ($detallePago->debe_haber || empty($detallePago->tabla)) {
                                    continue;
                                }

                                $planPago = PlanCuentas::find($detallePago->plan_cuentas_id);
                                if (!$planPago) {
                                    continue;
                                }

                                $valorCampoPago = (float) ($cuotaPago->{$detallePago->campo} ?? 0);
                                if ($valorCampoPago <= 0) {
                                    continue;
                                }

                                if ($planPago->prestamo) {
                                    $rangoTiempoPago = false;
                                    if ($diasPrestamos >= $planPago->tiempo_inicio && $diasPrestamos <= $planPago->tiempo_fin) {
                                        $rangoTiempoPago = true;
                                    }

                                    if (!$rangoTiempoPago && is_null($planPago->tiempo_fin)) {
                                        if ($diasPrestamos >= $planPago->tiempo_inicio) {
                                            $rangoTiempoPago = true;
                                        }
                                    }

                                    if (!$rangoTiempoPago) {
                                        continue;
                                    }
                                }

                                $valorPagoContable += $valorCampoPago;
                            }

                            $formasPagoAceptadas = RegistroFormasPago::where('company_id', Auth::user()->company_id)
                                ->where('letra_id', $cuotaPago->id)
                                ->whereIn('solicitado', [1, 3])
                                ->where(function ($query) {
                                    $query->whereNull('status')
                                        ->orWhereNotIn('status', [2, false, 0, '0']);
                                })
                                ->get();

                            $totalRegistroFormas = (float) $formasPagoAceptadas->sum('valor');
                            $movimientosFormasPagoIds = $formasPagoAceptadas
                                ->pluck('customer_movimientos_id')
                                ->filter()
                                ->unique()
                                ->values();

                            $totalMovimientosLetra = (float) CustomerMovimiento::where('company_id', Auth::user()->company_id)
                                ->where('credit_folder_details_id', $cuotaPago->id)
                                ->where('type_transaction_id', $movimientos->type_transaction_id)
                                ->when($movimientosFormasPagoIds->isNotEmpty(), function ($query) use ($movimientosFormasPagoIds) {
                                    $query->whereIn('id', $movimientosFormasPagoIds);
                                })
                                ->where(function ($query) {
                                    $query->whereNull('status')
                                        ->orWhereNotIn('status', [false, 0, '0']);
                                })
                                ->sum('valor_movimiento');

                            if ($totalRegistroFormas > 0) {
                                $totalMovimientosLetra = $totalRegistroFormas;
                            }

                            if ($totalMovimientosLetra <= 0) {
                                $totalMovimientosLetra = $valorPagoContable > 0 ? $valorPagoContable : (float) $movimientos->valor_movimiento;
                            }

                            $baseProporcionPagoCredito = max($totalMovimientosLetra, $valorPagoContable);

                            $movimientosPagoCredito = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                                ->where('credit_folder_details_id', $cuotaPago->id)
                                ->where('type_transaction_id', $movimientos->type_transaction_id)
                                ->when($movimientosFormasPagoIds->isNotEmpty(), function ($query) use ($movimientosFormasPagoIds) {
                                    $query->whereIn('id', $movimientosFormasPagoIds);
                                })
                                ->where(function ($query) {
                                    $query->whereNull('status')
                                        ->orWhereNotIn('status', [false, 0, '0']);
                                })
                                ->orderBy('id')
                                ->get();

                            if ($movimientosPagoCredito->isEmpty()) {
                                $movimientosPagoCredito = collect([$movimientos]);
                            }

                            foreach ($movimientosPagoCredito as $movimientoPagoCredito) {
                                $proporcionMovimiento = $baseProporcionPagoCredito > 0
                                    ? ((float) $movimientoPagoCredito->valor_movimiento / $baseProporcionPagoCredito)
                                    : 1;

                                foreach ($configDetalle as $key => $value) {
                                    if ($value->tabla == "" || $value->tabla == null) {
                                        $valorFinal = $valorPagoContable > 0
                                            ? round($valorPagoContable * $proporcionMovimiento, 2)
                                            : $movimientoPagoCredito->{$value->campo_foraneo};
                                    } else {
                                        $valorFinal = round(((float) ($cuotaPago->{$value->campo} ?? 0)) * $proporcionMovimiento, 2);
                                    }

                                    $planCuentas = PlanCuentas::find($value->plan_cuentas_id);
                                    if (!$planCuentas) {
                                        continue;
                                    }

                                    if ((float) $valorFinal <= 0) {
                                        continue;
                                    }

                                    $esCuentaMovimiento = self::esCuentaCaja($planCuentas) || self::esCuentaBanco($planCuentas);

                                    if ($planCuentas->prestamo && !$esCuentaMovimiento) {
                                        //Buscar Plan
                                        $rangoTiempo = false;
                                        if ($diasPrestamos >= $planCuentas->tiempo_inicio && $diasPrestamos <= $planCuentas->tiempo_fin) {
                                            $rangoTiempo = true;
                                        }

                                        if (!$rangoTiempo && is_null($planCuentas->tiempo_fin)) {
                                            if ($diasPrestamos >= $planCuentas->tiempo_inicio) {
                                                $rangoTiempo = true;
                                            }
                                        }

                                        if ($rangoTiempo) {
                                            $asientosDetalle = new AsientosDetalle();
                                            $asientosDetalle->company_id = Auth::user()->company_id;
                                            $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                            $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                            $asientosDetalle->valor = $valorFinal;
                                            $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientoPagoCredito->user_created_id);
                                            $asientosDetalle->debe_haber = $value->debe_haber;
                                            $asientosDetalle->fecha_contable = $movimientoPagoCredito->date_created . ' ' . $movimientoPagoCredito->hour_created;
                                            $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                            $asientosDetalle->user_created = $movimientoPagoCredito->user_created_id;
                                            $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                            $asientosDetalle->status = true;
                                            $asientosDetalle->save();
                                        }
                                    } else {
                                        $usaBanco = self::movimientoPagoCreditoUsaBanco($movimientoPagoCredito);
                                        $movimientoBancoId = $usaBanco ? ($movimientoPagoCredito->banco_id ?? null) : null;

                                        if (!$usaBanco && self::esCuentaBanco($planCuentas)) {
                                            continue;
                                        }

                                        if ($usaBanco && self::esCuentaCaja($planCuentas)) {
                                            continue;
                                        }

                                        if ($usaBanco && $planCuentas->banco_id !== null && (int) $movimientoBancoId !== (int) $planCuentas->banco_id) {
                                            continue;
                                        }

                                        $asientosDetalle = new AsientosDetalle();
                                        $asientosDetalle->company_id = Auth::user()->company_id;
                                        $asientosDetalle->asientos_header_id = $asientosHeader->id;
                                        $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                                        $asientosDetalle->valor = $valorFinal;
                                        $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientoPagoCredito->user_created_id);
                                        $asientosDetalle->debe_haber = $value->debe_haber;
                                        $asientosDetalle->fecha_contable = $movimientoPagoCredito->date_created . ' ' . $movimientoPagoCredito->hour_created;
                                        $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                                        $asientosDetalle->user_created = $movimientoPagoCredito->user_created_id;
                                        $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                                        $asientosDetalle->status = true;
                                        $asientosDetalle->save();
                                    }
                                }
                            }
                        }
                        if (!isset($prestamo->tipo_pago) || $prestamo->tipo_pago != "MENSUAL") {
                            self::eliminarAsientoTemporal($asientosHeader);

                            return [
                                'code' => '204',
                                'msg' => 'No se genero el asiento: el credito no tiene tipo de pago MENSUAL configurado.'
                            ];
                        }

                        break;

                    case 'EUS': //ENTREGA UTILIDADES A SOCIOS
                        $companyId = Auth::user()->company_id;
                        $asientosHeaderId = $asientosHeader->id;
                        $detalleHABER_historico = AsientosDetalle::where('company_id', $companyId)
                            ->where('asientos_header_id', $asientosHeaderId)
                            ->where('debe_haber', false)
                            ->first();
                        $planCuentasIdHABER = null;
                        if ($detalleHABER_historico) {
                            $planCuentasIdHABER = $detalleHABER_historico->plan_cuentas_id;
                        } else {
                            $utilidadConfig = Utilidad::where('company_id', $companyId)->first();
                            $planCuentasIdHABER = $utilidadConfig->cuenta_pasivo_id ?? null;
                        }
                        if (is_null($planCuentasIdHABER)) {
                            Log::error("EUS Recalcular Asientos fallo para AsientoHeader ID {$asientosHeaderId}: HABER cuenta no pudo ser determinada.");
                            $data = [
                                'code' => '500',
                                'msg' => 'Error al recalcular EUS: No se pudo determinar la cuenta del HABER.'
                            ];
                            return $data;
                        }
                        $detalleDEBE_historico = AsientosDetalle::where('company_id', $companyId)
                            ->where('asientos_header_id', $asientosHeaderId)
                            ->where('debe_haber', true)
                            ->first();
                        if ($detalleDEBE_historico) {
                            $planCuentasIdDEBE = $detalleDEBE_historico->plan_cuentas_id;
                        } else {
                            $detalleDEBEConfig = ConfigPlanDetalle::where('config_plan_header_id', $configHeader->id)
                                ->where('debe_haber', true)
                                ->where('status', 1)
                                ->first();
                            if (!$detalleDEBEConfig) {
                                Log::error("EUS Recalcular Asientos fallo no Se pudo determinar la cuenta del DEBE.");
                                $data = [
                                    'code' => '500',
                                    'msg' => 'Error al recalcular EUS: No se pudo determinar la cuenta del DEBE.'
                                ];
                                return $data;
                            }
                            $planCuentasIdDEBE = $detalleDEBEConfig->plan_cuentas_id;
                        }
                        AsientosDetalle::where('company_id', $companyId)->where('asientos_header_id', $asientosHeader->id)->delete();
                        // Crear el AsientosDetalle para el DEBE
                        $asientosDetalleDEBE = new AsientosDetalle();
                        $asientosDetalleDEBE->company_id = $companyId;
                        $asientosDetalleDEBE->asientos_header_id = $asientosHeader->id;
                        $asientosDetalleDEBE->plan_cuentas_id = $planCuentasIdDEBE;
                        $asientosDetalleDEBE->valor = $general ? $detalleDEBE_historico->valor : $movimientos->valor_movimiento;
                        $asientosDetalleDEBE->sedes_centro_costos_id = $general ? $detalleDEBE_historico->sedes_centro_costos_id : self::buscarSedesCentroCostos($movimientos->user_created_id);
                        $asientosDetalleDEBE->debe_haber = true;
                        $asientosDetalleDEBE->fecha_contable = $general ? $detalleDEBE_historico->fecha_contable : $movimientos->created_at;
                        $asientosDetalleDEBE->fecha_creacion = date('Y-m-d H:i:s');
                        $asientosDetalleDEBE->user_created = $general ? $detalleDEBE_historico->user_created : $movimientos->user_created_id;
                        $asientosDetalleDEBE->observacion = 'ASIENTO AUTOMATICO';
                        $asientosDetalleDEBE->status = true;
                        $asientosDetalleDEBE->save();
                        // Crear el AsientosDetalle para el HABER
                        $asientosDetalleHABER = new AsientosDetalle();
                        $asientosDetalleHABER->company_id = $companyId;
                        $asientosDetalleHABER->asientos_header_id = $asientosHeader->id;
                        $asientosDetalleHABER->plan_cuentas_id = $planCuentasIdHABER;
                        $asientosDetalleHABER->valor = $general ? $detalleHABER_historico->valor : $movimientos->valor_movimiento;
                        $asientosDetalleHABER->sedes_centro_costos_id = $general ? $detalleHABER_historico->sedes_centro_costos_id : self::buscarSedesCentroCostos($movimientos->user_created_id);
                        $asientosDetalleHABER->debe_haber = false;
                        $asientosDetalleHABER->fecha_contable = $general ? $detalleHABER_historico->fecha_contable : $movimientos->created_at;
                        $asientosDetalleHABER->fecha_creacion = date('Y-m-d H:i:s');
                        $asientosDetalleHABER->user_created = $general ? $detalleHABER_historico->user_created : $movimientos->user_created_id;
                        $asientosDetalleHABER->observacion = 'ASIENTO AUTOMATICO';
                        $asientosDetalleHABER->status = true;
                        $asientosDetalleHABER->save();

                        break;

                    default:
                        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $asientosHeader->id)->delete();
                        $detalleHaberNotaDebitoId = null;

                        if ($transaction->name_corto === 'DND') {
                            $detallesHaberNotaDebito = $configDetalle->filter(function ($detalle) {
                                if ($detalle->debe_haber) {
                                    return false;
                                }

                                $planCuentas = PlanCuentas::find($detalle->plan_cuentas_id);

                                return $planCuentas
                                    && !self::esCuentaCaja($planCuentas)
                                    && !self::esCuentaBanco($planCuentas);
                            });

                            if ($detallesHaberNotaDebito->count() !== 1) {
                                self::eliminarAsientoTemporal($asientosHeader);

                                return [
                                    'code' => '204',
                                    'msg' => 'No se genero el asiento: la Nota Debito debe tener una sola cuenta HABER propia configurada en DND, diferente a Caja y Bancos.'
                                ];
                            }

                            $detalleHaberNotaDebitoId = $detallesHaberNotaDebito->first()->id;
                        }

                        foreach ($configDetalle as $key => $value) {
                            if ($value->tabla == "" || $value->tabla == null) {
                                $valorFinal = $movimientos->{$value->campo_foraneo};
                            } else {
                                $idPago = (int) $movimientos->{$value->campo_foraneo};
                                $cuota = CreditFolderDetail::find($idPago);
                                $valorFinal = $cuota->{$value->campo};
                            }
                            if (is_null($valorFinal)) {
                                //dd('null');
                            }

                            $planCuentas = PlanCuentas::find($value->plan_cuentas_id);
                            if (!$planCuentas) {
                                continue;
                            }

                            if ($transaction->name_corto === 'DND' && !$value->debe_haber && $value->id !== $detalleHaberNotaDebitoId) {
                                continue;
                            }

                            $movimientoBancoId = $movimientos->banco_id ?? null;
                            if ($planCuentas->banco_id !== null && (int) $movimientoBancoId !== (int) $planCuentas->banco_id) {
                                continue;
                            }

                            if (self::esCuentaCaja($planCuentas) && $movimientoBancoId !== null) {
                                continue;
                            }

                            $asientosDetalle = new AsientosDetalle();
                            $asientosDetalle->company_id = Auth::user()->company_id;
                            $asientosDetalle->asientos_header_id = $asientosHeader->id;
                            $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
                            $asientosDetalle->valor = $valorFinal;
                            $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos($movimientos->user_created_id);
                            $asientosDetalle->debe_haber = $value->debe_haber;
                            $asientosDetalle->fecha_contable = $movimientos->date_created . ' ' . $movimientos->hour_created;
                            $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
                            $asientosDetalle->user_created = $movimientos->user_created_id;
                            $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
                            $asientosDetalle->status = true;
                            $asientosDetalle->save();
                        }
                        break;
                }
            }

            if (isset($asientosHeader) && !self::estaCuadrado($asientosHeader->id)) {
                $totales = self::totales($asientosHeader->id);
                AsientosDetalle::where('company_id', Auth::user()->company_id)
                    ->where('asientos_header_id', $asientosHeader->id)
                    ->delete();
                $asientosHeader->delete();

                return [
                    'code' => '203',
                    'msg' => 'El asiento automatico quedo descuadrado. Debe: ' . number_format($totales['debe'], 2, '.', '') . ' Haber: ' . number_format($totales['haber'], 2, '.', '') . '. Revise la configuracion contable del modulo.'
                ];
            }

            $data = [
                'code' => '200',
                'msg' => 'Asiento recalculado exitosamente'
            ];
        }
        return $data;
    }

    public static function recalcularAsientosBovedas($id)
    {
        $data = [];
        $descargo = DescargoBovedasHeader::find($id);
        if (!$descargo) {
            return [
                'code' => '404',
                'msg' => 'No existe el descargo de boveda para recalcular el asiento.'
            ];
        }

        if ($descargo->estado !== 'FINALIZADO' || in_array($descargo->status, [false, 0, '0'], true)) {
            return [
                'code' => '204',
                'msg' => 'No se genera asiento para descargos de boveda no finalizados.'
            ];
        }

        $configHeaderExist = ConfigPlanHeader::where('company_id', Auth::user()->company_id)
            ->where('operaciones_descargo_bovedas_id', $descargo->operaciones_descargo_bovedas_id)
            ->where('status', true)
            ->exists();

        if (!$configHeaderExist) {
            $data = [
                'code' => '201',
                'msg' => 'No existe configuracion del Plan de ese modulo'
            ];
            return $data;
        }
        $configHeader = ConfigPlanHeader::where('company_id', Auth::user()->company_id)
            ->where('operaciones_descargo_bovedas_id', $descargo->operaciones_descargo_bovedas_id)
            ->where('status', true)
            ->first();

        $configDetalleExist = ConfigPlanDetalle::where('company_id', Auth::user()->company_id)
            ->where('config_plan_header_id', $configHeader->id)
            ->where('status', true)
            ->exists();

        if (!$configDetalleExist) {
            $data = [
                'code' => '202',
                'msg' => 'No existe debe y haber en la configuración'
            ];
            return $data;
        }

        $configDetalle = ConfigPlanDetalle::where('company_id', Auth::user()->company_id)
            ->where('config_plan_header_id', $configHeader->id)
            ->where('status', true)
            ->orderBy('debe_haber', 'DESC')
            ->get();

        $existeAsientoHeader = AsientosHeader::where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->where('descargo_bovedas_header_id', $id)
            ->exists();
        if ($existeAsientoHeader) {
            $asientosHeader = AsientosHeader::where('company_id', Auth::user()->company_id)
                ->where('status', true)
                ->where('descargo_bovedas_header_id', $id)
                ->first();
        } else {
            $asientosHeader = new AsientosHeader();
            $asientosHeader->company_id = Auth::user()->company_id;
        }

        $asientosHeader->manual = false;
        $asientosHeader->fecha_contable = $descargo->fecha_creacion;
        $asientosHeader->fecha_creacion = date('Y-m-d H:i:s');
        $asientosHeader->user_created = Auth::user()->id;
        $asientosHeader->concepto_id = $configHeader->concepto_id;
        $asientosHeader->config_plan_header_id = $configHeader->id;
        $asientosHeader->descripcion = 'ASIENTO AUTOMATICO';
        $asientosHeader->descargo_bovedas_header_id = $id;
        $asientosHeader->status = true;
        $asientosHeader->save();
        //vaciar detalle
        AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $asientosHeader->id)->delete();
        foreach ($configDetalle as $key => $value) {
            $valor = $descargo->{$value->campo_foraneo};
            $planCuentas = PlanCuentas::find($value->plan_cuentas_id);
            if (!$planCuentas) {
                continue;
            }

            if ($planCuentas->banco_id !== null && (int) $descargo->bancos_id !== (int) $planCuentas->banco_id) {
                continue;
            }

            $asientosDetalle = new AsientosDetalle();
            $asientosDetalle->company_id = Auth::user()->company_id;
            $asientosDetalle->asientos_header_id = $asientosHeader->id;
            $asientosDetalle->plan_cuentas_id = $value->plan_cuentas_id;
            $asientosDetalle->valor = $valor;
            $asientosDetalle->sedes_centro_costos_id = AsientosHeader::buscarSedesCentroCostos(Auth::user()->id);
            $asientosDetalle->debe_haber = $value->debe_haber;
            $asientosDetalle->fecha_contable = $descargo->fecha_creacion;
            $asientosDetalle->fecha_creacion = date('Y-m-d H:i:s');
            $asientosDetalle->user_created = Auth::user()->id;
            $asientosDetalle->observacion = 'ASIENTO AUTOMATICO';
            $asientosDetalle->status = true;
            $asientosDetalle->save();
        }

        if (!self::estaCuadrado($asientosHeader->id)) {
            AsientosDetalle::where('company_id', Auth::user()->company_id)
                ->where('asientos_header_id', $asientosHeader->id)
                ->delete();
            $asientosHeader->delete();

            return [
                'code' => '203',
                'msg' => 'El asiento automatico de bovedas quedo descuadrado. Revise la configuracion contable.'
            ];
        }

        $data = [
            'code' => '200',
            'msg' => 'Asiento recalculado exitosamente'
        ];
        return $data;
    }

    public static function borrarAsientos($id, $table = 'customer_movimientos')
    {
        $company = Company::find(Auth::user()->company_id);
        switch ($table) {
            case 'customer_movimientos':
                $asientosHeader = AsientosHeader::where('customer_movimiento_id', $id)->first();
                break;
            case 'gastos':
                $asientosHeader = AsientosHeader::where('gasto_id', $id)->first();
                break;
        }

        if ($asientosHeader !== null) {
            $fechaContable = date('Y-m-d', strtotime($asientosHeader->fecha_contable));
            if ($asientosHeader !== null && $company && $company->puedeContabilizar($fechaContable)) {
                AsientosDetalle::where('company_id', Auth::user()->company_id)->where('asientos_header_id', $asientosHeader->id)->delete();
                $asientosHeader->delete();
            }
        }

        return true;
    }

    public static function buscarSedesCentroCostos($userId = null)
    {
        $sedeCentroCostoId = null;
        if ($userId !== null) {
            $user = User::find($userId);
            if (isset($user->sede_id)) {
                $sedeCentroCostoId = SedesCentroCostos::where('sede_id', $user->sede_id)->value('id');
            }
        }
        return $sedeCentroCostoId;
    }

    private static function eliminarAsientoTemporal($asientosHeader)
    {
        if (!$asientosHeader || !$asientosHeader->id) {
            return;
        }

        AsientosDetalle::where('company_id', Auth::user()->company_id)
            ->where('asientos_header_id', $asientosHeader->id)
            ->delete();

        $asientosHeader->delete();
    }

    public static function totales($asientosHeaderId)
    {
        $totales = AsientosDetalle::select(
            DB::raw("SUM(CASE WHEN debe_haber = 1 THEN valor ELSE 0 END) as debe"),
            DB::raw("SUM(CASE WHEN debe_haber = 0 THEN valor ELSE 0 END) as haber")
        )
            ->where('company_id', Auth::user()->company_id)
            ->where('asientos_header_id', $asientosHeaderId)
            ->where('status', true)
            ->first();

        return [
            'debe' => round((float) ($totales->debe ?? 0), 2),
            'haber' => round((float) ($totales->haber ?? 0), 2),
        ];
    }

    public static function estaCuadrado($asientosHeaderId)
    {
        $totales = self::totales($asientosHeaderId);
        return number_format($totales['debe'], 2, '.', '') === number_format($totales['haber'], 2, '.', '')
            && $totales['debe'] > 0;
    }

    private static function obtenerConceptoIdPorNombre($nombre)
    {
        $concepto = Conceptos::where('company_id', Auth::user()->company_id)
            ->where('nombre', $nombre)
            ->first();

        return $concepto ? $concepto->id : null;
    }

    private static function esCuentaCaja($planCuentas)
    {
        return $planCuentas && strpos((string) $planCuentas->codigo, '1.1.01') === 0;
    }

    private static function esCuentaBanco($planCuentas)
    {
        return $planCuentas && strpos((string) $planCuentas->codigo, '1.1.03') === 0;
    }

    private static function movimientoPagoCreditoUsaBanco($movimiento)
    {
        $formaPagoName = strtoupper(trim((string) ($movimiento->forma_pago_name ?? '')));

        if ($formaPagoName === '' && !empty($movimiento->forma_pago_id)) {
            $formaPago = FormasPago::find($movimiento->forma_pago_id);
            $formaPagoName = strtoupper(trim((string) ($formaPago->nombre ?? '')));
        }

        return $formaPagoName === 'TRANSFERENCIA';
    }
}
