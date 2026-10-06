<?php

namespace App\Http\Livewire\SolicitudPagosLiquidacion;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\{AsientosHeader, Bancos, Company, CreditFolderDetail, CreditFolderHeader, Customer, CustomerHistorial, CustomerMovimiento, RegistroFormasLiquidacion, TypeTransaction};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitudPagosLiquidacion extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $buscarCedula = '', $estadoFiltro = '', $detallePagos = [], $registro = null;
    public $liquidacionSeleccionada = null, $observacionRechazo = '', $mostrarFormularioRechazo = false;

    public function updatingBuscarCedula()
    {
        $this->resetPage();
    }
    public function updatingEstadoFiltro()
    {
        $this->resetPage();
    }
    public function limpiarFiltros()
    {
        $this->reset('buscarCedula', 'estadoFiltro');
        $this->resetPage();
    }

    private function solicitud($id, $fecha, $hora)
    {
        return RegistroFormasLiquidacion::where('company_id', Auth::user()->company_id)
            ->where('liquidacion_id', $id)->where('fecha_solicitud', $fecha)->where('hora_solicitud', $hora);
    }

    public function cargarDatosLiquidacion($id, $fecha, $hora)
    {
        $this->resetErrorBag();
        $this->reset('registro', 'detallePagos', 'liquidacionSeleccionada', 'observacionRechazo', 'mostrarFormularioRechazo');
        $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $customer = Customer::where('company_id', Auth::user()->company_id)->findOrFail($header->customer_id);
        $pagos = $this->solicitud($id, $fecha, $hora)->with('banco')->orderBy('id')->get();
        if ($pagos->isEmpty()) $this->fallar('No se encontró la solicitud de liquidación.');
        $registro = $pagos->first();
        foreach (['nombres', 'apellidos', 'numero_documento'] as $field) $registro->$field = $customer->$field;
        $registro->codeFolderHeader = $header->code;
        $registro->valor_solicitado = $header->valor_solicitado;
        $registro->total_pagando = $header->total_pagando;
        $transferencias = $pagos->filter(function ($p) {
            return $this->esTransferencia($p);
        });
        $registro->estado_solicitud = (int) ($transferencias->isEmpty() ? $pagos : $transferencias)->max('solicitado');
        $rechazado = $pagos->firstWhere('solicitado', 2);
        if ($rechazado) $registro->observacion = $rechazado->observacion;
        $this->registro = $registro->toArray();
        $this->detallePagos = $pagos;
        $this->liquidacionSeleccionada = $header->id;
    }

    private function esTransferencia($pago)
    {
        return strtoupper(trim($pago->forma_pago)) === 'TRANSFERENCIA';
    }
    public function abrirFormularioRechazo()
    {
        $this->resetErrorBag();
        $this->observacionRechazo = '';
        $this->mostrarFormularioRechazo = true;
    }
    public function aprobarSolicitud($id, $fecha, $hora)
    {
        $this->procesar($id, $fecha, $hora, false);
    }
    public function rechazarSolicitud($id, $fecha, $hora)
    {
        $this->observacionRechazo = trim($this->observacionRechazo);
        $this->validate(['observacionRechazo' => 'required|string|max:1000'], [], ['observacionRechazo' => 'motivo del rechazo']);
        $this->procesar($id, $fecha, $hora, true);
    }

    private function procesar($id, $fecha, $hora, $rechazar)
    {
        $this->resetErrorBag('solicitud');
        try {
            DB::transaction(function () use ($id, $fecha, $hora, $rechazar) {
                $company = Company::where('id', Auth::user()->company_id)->lockForUpdate()->firstOrFail();
                $header = CreditFolderHeader::where('company_id', $company->id)->lockForUpdate()->findOrFail($id);
                $customer = Customer::where('company_id', $company->id)->findOrFail($header->customer_id);
                $pagos = $this->solicitud($id, $fecha, $hora)->lockForUpdate()->orderBy('id')->get();
                if ($pagos->isEmpty() || $pagos->contains(function ($p) {
                    return (int) $p->solicitado !== 3 || $p->customer_movimientos_id;
                })) {
                    $this->fallar('La solicitud ya fue procesada o no está pendiente. Actualiza la lista.');
                }
                if ($pagos->contains(function ($p) use ($customer) {
                    return $p->customer_id != $customer->id || !is_numeric($p->valor) || $p->valor <= 0;
                })) {
                    $this->fallar('La solicitud tiene datos de pago inválidos.');
                }
                if ($rechazar && !$pagos->contains(function ($p) {
                    return $this->esTransferencia($p);
                })) $this->fallar('No existen transferencias para rechazar.');
                $aceptados = $rechazar ? $pagos->reject(function ($p) {
                    return $this->esTransferencia($p);
                }) : $pagos;
                $valor = round($aceptados->sum('valor'), 2);
                if ($aceptados->isNotEmpty()) {
                    $transaction = TypeTransaction::where('company_id', $company->id)->where('name_corto', 'LIC')->first();
                    if (!$transaction) $this->fallar('No se encontró la transacción LIC de liquidación.');
                    $detalles = CreditFolderDetail::where('company_id', $company->id)->where('code_folder_header', $header->code)
                        ->where('status', 'PENDIENTE')->orderBy('numero_cuota')->lockForUpdate()->get();
                    if ($detalles->isEmpty() || $header->status === 'PAGADO') $this->fallar('El crédito ya no tiene cuotas pendientes.');
                    if ($rechazar) $this->aplicarAbono($detalles, $aceptados, $valor, $header);
                    else $this->aplicarLiquidacion($detalles, $aceptados, $valor, $header);
                    foreach ($aceptados as $pago) $this->registrarMovimiento($pago, $customer, $header, $transaction, $company);
                    $header->total_pagando = round($header->total_pagando + $valor, 2);
                    $pendientes = CreditFolderDetail::where('company_id', $company->id)->where('code_folder_header', $header->code)->where('status', 'PENDIENTE')->exists();
                    $header->status = $pendientes ? 'ENTREGADO' : 'PAGADO';
                    $header->save();
                }
                foreach ($pagos as $pago) {
                    $pago->solicitado = $rechazar && $this->esTransferencia($pago) ? 2 : 1;
                    $pago->usuario_solicitud = Auth::user()->username;
                    $pago->usuario_id_solicitud = Auth::id();
                    $pago->fecha_aprobacion = date('Y-m-d');
                    $pago->hora_aprobacion = date('H:i:s');
                    if ((int) $pago->solicitado === 2) $pago->observacion = 'TRANSFERENCIA RECHAZADA: ' . $this->observacionRechazo;
                    $pago->save();
                }
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $this->fallar('No se pudo completar la operación. Los cambios se revirtieron; revisa la configuración o contacta al administrador.');
        }
        $this->reset('registro', 'detallePagos', 'liquidacionSeleccionada', 'observacionRechazo', 'mostrarFormularioRechazo');
        $this->dispatchBrowserEvent('close-modal-liquidacion');
        $this->dispatchBrowserEvent('alerta-liquidacion', ['tipo' => $rechazar ? 'warning' : 'success', 'mensaje' => $rechazar
            ? 'Transferencias rechazadas. Las otras formas de pago recibidas se conservaron como abono al crédito.'
            : 'La liquidación fue aprobada y registrada correctamente.']);
    }

    private function aplicarLiquidacion($detalles, $pagos, $valor, $header)
    {
        // Mantener la regla de intereses usada por Créditos al solicitar la liquidación.
        $limite = $header->cuotas_pagar <= 12 ? 6 : (int) ceil($header->cuotas_pagar / 2);
        $importe = function ($d) use ($limite) {
            return round($d->capital_amortizado + $d->fondo_desgravamen + ($d->numero_cuota <= $limite ? $d->interes_periodo : 0), 2);
        };
        if (abs($valor - round($detalles->sum($importe), 2)) > 0.009) $this->fallar('El importe solicitado no coincide con la liquidación actual del crédito. Revisa las cuotas antes de aprobar.');
        foreach (
            $pagos->filter(function ($p) {
                return $this->esTransferencia($p);
            }) as $pago
        ) {
            if (!$pago->banco_id || !trim((string) $pago->numero_comprobante)) $this->fallar('La transferencia requiere banco y número de comprobante.');
            if (!Bancos::where('company_id', Auth::user()->company_id)->where('id', $pago->banco_id)->exists()) $this->fallar('El banco no pertenece a la empresa.');
            $duplicado = CustomerMovimiento::where('company_id', Auth::user()->company_id)->where('banco_id', $pago->banco_id)
                ->where('comprobante', $pago->numero_comprobante)->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', 1);
                })->exists();
            $enCuota = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('banco_id', $pago->banco_id)
                ->where('numero_comprobante', $pago->numero_comprobante)->whereNotIn('id', $detalles->pluck('id'))->exists();
            $enSolicitud = $pagos->filter(function ($p) use ($pago) {
                return $this->esTransferencia($p) && $p->banco_id == $pago->banco_id && $p->numero_comprobante === $pago->numero_comprobante;
            })->count() > 1;
            if ($duplicado || $enCuota || $enSolicitud) $this->fallar('El comprobante de transferencia ya está registrado.');
        }
        $transferencia = $pagos->first(function ($p) {
            return $this->esTransferencia($p);
        });
        foreach ($detalles as $detalle) {
            $detalle->valor_pagado = $importe($detalle);
            $detalle->valor_final = $detalle->valor_pagado;
            $detalle->status = 'PAGADA';
            $detalle->saldo_remanente = 0;
            $detalle->faltante_anterior_cuota = 0;
            $detalle->faltante_prox_cuota = 0;
            $ultimo = $transferencia && $detalle->id === $detalles->last()->id;
            $detalle->banco_id = $ultimo ? $transferencia->banco_id : null;
            $detalle->numero_comprobante = $ultimo ? $transferencia->numero_comprobante : null;
            $this->guardarDetalle($detalle, $pagos, 'PAGA LA TOTALIDAD DEL CREDITO ' . $header->code);
        }
    }

    private function aplicarAbono($detalles, $pagos, $valor, $header)
    {
        $saldo = $valor;
        foreach ($detalles as $detalle) {
            if ($saldo <= 0) break;
            $pendiente = max(0, round($detalle->valor_cuota - $detalle->valor_pagado, 2));
            if ($pendiente <= 0) continue;
            $abono = min($saldo, $pendiente);
            $detalle->valor_pagado = round($detalle->valor_pagado + $abono, 2);
            $detalle->valor_final = $detalle->valor_pagado;
            $faltante = round($pendiente - $abono, 2);
            $detalle->status = $faltante > 0 ? 'PENDIENTE' : 'PAGADA';
            $detalle->saldo_remanente = $faltante;
            $detalle->faltante_anterior_cuota = $faltante;
            $detalle->faltante_prox_cuota = 0;
            $this->guardarDetalle($detalle, $pagos, 'ABONO POR LIQUIDACION CON TRANSFERENCIA RECHAZADA ' . $header->code);
            $saldo = round($saldo - $abono, 2);
        }
        if ($saldo > 0) $this->fallar('El abono supera las cuotas pendientes. Revisa los importes de la solicitud.');
    }

    private function guardarDetalle($detalle, $pagos, $observacion)
    {
        $detalle->tipo_pago = $pagos->pluck('forma_pago')->unique()->implode(' + ');
        $detalle->date_pay = date('Y-m-d');
        $detalle->hour_pay = date('H:i:s');
        $detalle->user_pay_id = Auth::id();
        $detalle->obervation_pago = $observacion . ' ' . date('Y-m-d');
        $detalle->save();
    }

    private function registrarMovimiento($pago, $customer, $header, $transaction, $company)
    {
        $historial = CustomerHistorial::where('company_id', $company->id)->where('status', 1)->whereNotNull('customer_movimiento_code')->where('customer_movimiento_code', '!=', '');
        $saldo = max(0, (clone $historial)->where('type_transaction_action', 'S')->sum('valor_movimiento') - (clone $historial)->where('type_transaction_action', 'R')->sum('valor_movimiento'));
        $movimiento = CustomerMovimiento::create([
            'code' => BaseController::generarCodigo('customer_movimientos', 9),
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'customer_code' => $customer->code,
            'credit_folder_header_id' => $header->id,
            'afecta' => $transaction->afecta,
            'customer_name' => $customer->nombres . ' ' . $customer->apellidos,
            'customer_ruc' => $customer->numero_documento,
            'customer_address' => $customer->direccion,
            'customer_telefono' => $customer->telefono,
            'type_transaction_id' => $transaction->id,
            'type_transaction_name' => $transaction->name,
            'type_transaction_action' => $transaction->action,
            'valor_movimiento' => $pago->valor,
            'saldo_general' => $saldo + $pago->valor,
            'observation' => 'PAGO POR LIQUIDACION DEL CREDITO ' . $header->code,
            'user_created_id' => Auth::id(),
            'user_created_name' => Auth::user()->username,
            'date_created' => date('Y-m-d'),
            'hour_created' => date('H:i:s'),
            'forma_pago_id' => $pago->forma_pago_id,
            'forma_pago_name' => $pago->forma_pago,
            'banco_id' => $pago->banco_id,
            'comprobante' => $pago->numero_comprobante,
        ]);
        if ($company->puedeContabilizar(date('Y-m-d'))) {
            $resultado = AsientosHeader::recalcularAsientos($movimiento->id, 'customer_movimientos');
            if (($resultado['code'] ?? null) != '200') $this->fallar('No se pudo crear el asiento contable. Revisa la configuración de liquidación; el pago no fue registrado.');
        }
        CustomerHistorialController::guardarHistorialAutomatica($movimiento->code, $transaction->id, $pago->valor, $customer->code, $movimiento->saldo_general, date('Y-m-d'));
        $pago->customer_movimientos_id = $movimiento->id;
    }

    private function fallar($mensaje)
    {
        throw ValidationException::withMessages(['solicitud' => $mensaje]);
    }

    public function render()
    {
        $estado = "COALESCE(MAX(CASE WHEN UPPER(TRIM(registro_formas_liquidacion.forma_pago)) = 'TRANSFERENCIA' THEN registro_formas_liquidacion.solicitado ELSE NULL END), MAX(registro_formas_liquidacion.solicitado))";
        $liquidaciones = RegistroFormasLiquidacion::query()
            ->join('credit_folder_headers', 'credit_folder_headers.id', '=', 'registro_formas_liquidacion.liquidacion_id')
            ->join('customer', 'customer.id', '=', 'registro_formas_liquidacion.customer_id')
            ->where('registro_formas_liquidacion.company_id', Auth::user()->company_id)->where('credit_folder_headers.company_id', Auth::user()->company_id)->where('customer.company_id', Auth::user()->company_id)
            ->whereIn('registro_formas_liquidacion.solicitado', [1, 2, 3])
            ->select('registro_formas_liquidacion.liquidacion_id', 'registro_formas_liquidacion.customer_id', 'registro_formas_liquidacion.fecha_solicitud', 'registro_formas_liquidacion.hora_solicitud', 'customer.nombres', 'customer.apellidos', 'customer.numero_documento', 'credit_folder_headers.code as codeFolderHeader')
            ->selectRaw("MAX(registro_formas_liquidacion.user_name) AS user_name, MAX(registro_formas_liquidacion.usuario_solicitud) AS usuario_solicitud,
                SUM(CASE WHEN UPPER(TRIM(registro_formas_liquidacion.forma_pago)) = 'TRANSFERENCIA' THEN registro_formas_liquidacion.valor ELSE 0 END) AS valor_transferencia,
                SUM(CASE WHEN UPPER(TRIM(registro_formas_liquidacion.forma_pago)) <> 'TRANSFERENCIA' THEN registro_formas_liquidacion.valor ELSE 0 END) AS valor_otros,
                SUM(registro_formas_liquidacion.valor) AS valor_total, $estado AS estado_solicitud")
            ->when(trim($this->buscarCedula) !== '', function ($query) {
                $buscar = '%' . trim($this->buscarCedula) . '%';
                $query->where(function ($q) use ($buscar) {
                    $q->where('customer.numero_documento', 'like', $buscar)->orWhere('customer.nombres', 'like', $buscar)->orWhere('customer.apellidos', 'like', $buscar)->orWhere('credit_folder_headers.code', 'like', $buscar);
                });
            })
            ->groupBy('registro_formas_liquidacion.liquidacion_id', 'registro_formas_liquidacion.customer_id', 'registro_formas_liquidacion.fecha_solicitud', 'registro_formas_liquidacion.hora_solicitud', 'customer.nombres', 'customer.apellidos', 'customer.numero_documento', 'credit_folder_headers.code')
            ->when(in_array((string) $this->estadoFiltro, ['1', '2', '3'], true), function ($query) use ($estado) {
                $query->havingRaw($estado . ' = ?', [(string) $this->estadoFiltro]);
            })
            ->orderByDesc('registro_formas_liquidacion.fecha_solicitud')->orderByDesc('registro_formas_liquidacion.hora_solicitud')->paginate(10);
        return view('livewire.solicitud-pagos-liquidacion.solicitud-pagos-liquidacion', compact('liquidaciones'));
    }
}
