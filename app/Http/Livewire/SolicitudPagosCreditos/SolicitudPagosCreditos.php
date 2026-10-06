<?php

namespace App\Http\Livewire\SolicitudPagosCreditos;

use App\Http\Controllers\Base\BaseController as BaseBaseController;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RegistroFormasPago;
use Illuminate\Support\Facades\DB;


use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\AsientosHeader;
use App\Models\TypeTransaction;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController as CustomerCustomerHistorialController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class SolicitudPagosCreditos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscarCedula = '';
    public $estadoFiltro = '3';

    public function updatingEstadoFiltro()
    {
        $this->resetPage();
    }

    private function cuotaEmpresa($id, $lock = false)
    {
        $query = CreditFolderDetail::where('company_id', Auth::user()->company_id);
        if ($lock) $query->lockForUpdate();
        return $query->findOrFail($id);
    }

    private function pagosPendientes($letraId)
    {
        return RegistroFormasPago::where('company_id', Auth::user()->company_id)
            ->where('company_id', Auth::user()->company_id)->where('letra_id', $letraId)->where('status', 3)->lockForUpdate()->get();
    }

    private function validarSolicitudPendiente($pagos)
    {
        if ($pagos->isEmpty()) {
            throw ValidationException::withMessages(['solicitud' => 'La solicitud ya fue procesada o no tiene pagos pendientes.']);
        }
    }

    private function limpiarDetalle()
    {
        $this->reset(['registro', 'detallePagos', 'letraSeleccionada', 'aplicarInteresMora',
            'interesMoraOriginal', 'totalCheque', 'totalTransferencia', 'totalEfectivo', 'totalOtros',
            'totalRecibido', 'totalAprobar']);
        $this->dispatchBrowserEvent('close-modal-pagos');
    }
    public $detallePagos = [];
    public $registro = null;
    public $letraSeleccionada;
    public $aplicarInteresMora = true;
    public $interesMoraOriginal = 0;
    public $errorPago = null;
    private $errorContableNotificado = false;

    public $totalCheque = 0;
    public $totalTransferencia = 0;
    public $totalEfectivo = 0;
    public $totalOtros = 0;

    public $totalRecibido = 0;
    public $totalAprobar = 0;
    //public $observacionRechazo = '';

    public function updatingBuscarCedula()
    {
        $this->resetPage();
    }

    private function esTransferencia($formaPago): bool
    {
        return strtoupper(trim((string) $formaPago)) === 'TRANSFERENCIA';
    }

    private function esEfectivo($formaPago): bool
    {
        return strtoupper(trim((string) $formaPago)) === 'EFECTIVO';
    }

    public function cargarDatosPago($letraId, $esCargaInicial = true)
    {
        $cuota = $this->cuotaEmpresa($letraId);
        $this->errorPago = null;
        $this->resetValidation();
        $this->letraSeleccionada = $letraId;
        $pendientes = RegistroFormasPago::where('company_id', Auth::user()->company_id)
            ->where('letra_id', $letraId)->where('status', 3)->exists();
        $estadoDetalle = $pendientes ? 3 : ($this->estadoFiltro === '2' ? 2 : 1);


        $this->detallePagos = RegistroFormasPago::select(
            'registro_formas_pagos.*',
            'credit_folder_details.path'
        )
            ->leftJoin(
                'credit_folder_details',
                'credit_folder_details.id',
                '=',
                'registro_formas_pagos.letra_id'
            )
            ->where('registro_formas_pagos.company_id', Auth::user()->company_id)
            ->where('registro_formas_pagos.letra_id', $letraId)
            ->where('registro_formas_pagos.status', $estadoDetalle)
            ->with('banco')->orderBy('registro_formas_pagos.id')
            ->get();

        $this->totalCheque = 0;
        $this->totalTransferencia = 0;
        $this->totalEfectivo = 0;
        $this->totalOtros = 0;

        foreach ($this->detallePagos as $pago) {
            switch (strtoupper(trim($pago->forma_pago))) {
                case 'CHEQUE':
                    $this->totalCheque += $pago->valor;
                    break;
                case 'TRANSFERENCIA':
                    $this->totalTransferencia += $pago->valor;
                    break;
                case 'EFECTIVO':
                    $this->totalEfectivo += $pago->valor;
                    break;
                default:
                    $this->totalOtros += $pago->valor;
                    break;
            }
        }

        $this->totalRecibido =
            $this->totalCheque
            + $this->totalTransferencia
            + $this->totalEfectivo
            + $this->totalOtros;

        $this->registro = RegistroFormasPago::select(
            'registro_formas_pagos.*',
            DB::raw("(SELECT numero_cuota FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as numeroCuota"),
            DB::raw("(SELECT code_folder_header FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as codeFolderHeader"),
            DB::raw("(SELECT interes_mora FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as interes_mora"),
            DB::raw("(SELECT nombres FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as nombres"),
            DB::raw("(SELECT apellidos FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as apellidos"),
            DB::raw("(SELECT numero_documento FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as numero_documento")
        )
            ->where('company_id', Auth::user()->company_id)->where('letra_id', $letraId)
            ->where('status', $estadoDetalle)->orderByDesc('id')->first();

        if ($this->registro) {
            $this->interesMoraOriginal = (float)$this->registro->interes_mora;

            // Aquí se decide el check de forma inteligente
            if ($esCargaInicial) {
                $this->aplicarInteresMora = $this->interesMoraOriginal > 0;
            }

            $this->actualizarTotalAprobar();
        }
    }

    public function actualizarTotalAprobar()
    {
        // Waiving late interest changes the amount due, never the amount actually received.
        $this->totalAprobar = round((float) $this->totalRecibido, 2);
    }
    public function updatedAplicarInteresMora()
    {
        if ($this->letraSeleccionada) {
            // Ejecutamos tu función para mantener los datos del cliente frescos
            $this->cargarDatosPago($this->letraSeleccionada, false);
        } else {
            $this->actualizarTotalAprobar();
        }
    }

    public function aprobarSolicitud($letraId)
    {
        DB::transaction(function () use ($letraId) {
            $cuota = $this->cuotaEmpresa($letraId, true);
            $pagos = $this->pagosPendientes($letraId);
            $this->validarSolicitudPendiente($pagos);
            $this->interesMoraOriginal = (float) $cuota->interes_mora;
            $total = round((float) $pagos->sum('valor'), 2);

            if ($total <= 0) {
                throw ValidationException::withMessages(['solicitud' => 'El importe a aprobar debe ser mayor que cero.']);
            }
            if (!$this->pagarLetra($total, $letraId, true)) {
                throw ValidationException::withMessages(['solicitud' => $this->errorPago]);
            }
        });
        $this->limpiarDetalle();
    }
    public function rechazarSolicitud($letraId)
    {
        DB::transaction(function () use ($letraId) {
            $cuota = $this->cuotaEmpresa($letraId, true);
            $pagos = $this->pagosPendientes($letraId);
            $this->validarSolicitudPendiente($pagos);
            $pagosEfectivo = $pagos->filter(function ($pago) { return $this->esEfectivo($pago->forma_pago); });
            foreach ($pagos as $pago) {
                if (!$this->esEfectivo($pago->forma_pago)) {
                    $pago->solicitado = 2;
                    $pago->status = 2;
                } else {
                    $pago->solicitado = 1;
                }
                $pago->usuario_solicitud = Auth::user()->username;
                $pago->usuario_id_solicitud = Auth::user()->id;
                $pago->fecha_solicitud = date('Y-m-d');
                $pago->hora_solicitud = date('H:i:s');
                $pago->save();
            }
            if ($pagosEfectivo->isEmpty()) {
                $cuota->status = 'PENDIENTE';
                $cuota->tipo_pago = 'EFECTIVO';
                $cuota->valor_pagado = 0;
                $cuota->valor_final = 0;
                $cuota->date_pay = null;
                $cuota->hour_pay = null;
                $cuota->user_pay_id = null;
                $cuota->save();
            } else {
                $this->interesMoraOriginal = (float) $cuota->interes_mora;
                if (!$this->pagarLetra(round((float) $pagosEfectivo->sum('valor'), 2), $letraId, false)) {
                    throw ValidationException::withMessages(['solicitud' => $this->errorPago]);
                }
            }
        });
        $this->limpiarDetalle();
        $this->dispatchBrowserEvent('alerta-pago-credito', [
            'titulo' => 'Solicitud revisada', 'tipo' => 'info',
            'mensaje' => 'Se rechazaron los pagos no efectivos. El efectivo, si existe, se conserva y se aplica a la cuota.',
        ]);
    }

    private function pagarLetra($valorPagar, $letraId, $incluyeTransferencia = true)
    {
        $this->errorPago = null;
        $this->errorContableNotificado = false;
        $etapa = 'buscar la cuota';
        DB::beginTransaction();
        try {
            $cuota = $this->cuotaEmpresa($letraId, true);
            $cabecera = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('code', $cuota->code_folder_header)->lockForUpdate()->firstOrFail();

            $cuota->interes_mora = $this->aplicarInteresMora
                ? $this->interesMoraOriginal
                : 0;

            $interesMora = $cuota->interes_mora ?? 0; // Usamos el valor que ya tenga la cuota

            // Lógica de cálculo idéntica a tu realizarPago()
            $pagoTotalReq = (float) round(($cuota->valor_cuota + $interesMora + $cuota->faltante_anterior_cuota - $cuota->saldo_anterior_cuota), 2);

            // Determinamos el CASO
            $caso = 1;
            if ($valorPagar > $pagoTotalReq) $caso = 2;
            if ($valorPagar < $pagoTotalReq) $caso = 3;

            // Actualización de la cuota principal
            $cuota->valor_pagado = $valorPagar;
            $cuota->valor_final = $valorPagar;
            $cuota->date_pay = date('Y-m-d');
            $cuota->hour_pay = date('H:i:s');
            $cuota->user_pay_id = Auth::user()->id;
            $cuota->status = 'PAGADA';
            $cuota->adelanto_prox_cuota = 0;
            $cuota->faltante_prox_cuota = 0;

            if ($caso == 2) {
                $SOBRANTE = (float) round($valorPagar - $pagoTotalReq, 2);
                $cuota->adelanto_prox_cuota = $SOBRANTE;
            }

            if ($caso == 3) {
                $FALTANTE = (float) round($pagoTotalReq - $valorPagar, 2);
                $cuota->faltante_prox_cuota = $FALTANTE;
            }

            $etapa = 'guardar la cuota';
            if (!$cuota->save()) {
                throw new \RuntimeException('El guardado de la cuota fue cancelado.');
            }

            $etapa = 'distribuir el sobrante o faltante';
            // La cuota actual debe estar pagada antes de buscar cuotas pendientes.
            if ($caso == 2) {
                $this->procesarSobrante($cuota->code_folder_header, $SOBRANTE);
            }

            if ($caso == 3) {
                $this->procesarFaltante($cuota->code_folder_header, $FALTANTE);
            }

            if ($incluyeTransferencia) {
                $etapa = 'aprobar las formas de pago';
                RegistroFormasPago::where('letra_id', $letraId)
                    ->where('status', 3)
                    ->update([
                        'solicitado' => 1,
                        'usuario_solicitud' => Auth::user()->username,
                        'usuario_id_solicitud' => Auth::user()->id,
                        'fecha_solicitud' => date('Y-m-d'),
                        'hora_solicitud' => date('H:i:s'),
                    ]);
            }

            // Ejecutamos tus funciones de transición y cierre
            $etapa = 'actualizar el estado del credito';
            $this->manejarTransicionEstado($cuota);
            $this->ultimaLetraPago($letraId);

            // Actualizar Cabecera
            $etapa = 'actualizar el total del credito';
            $cabecera->total_pagando += $valorPagar;
            $cabecera->save();

            $customer = Customer::where('company_id', Auth::user()->company_id)->findOrFail($cabecera->customer_id);

            $pagos = RegistroFormasPago::where('letra_id', $letraId)
                ->where('status', 3);
            $movimientosCreados = [];
            $etapa = 'registrar los movimientos e historial del pago';

            if ($incluyeTransferencia) { // caso todos los pagos incluidos
                foreach ($pagos->get() as $pago) {

                    $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'PC')
                        ->firstOrFail();
                    $tabla = 'customer_movimientos';
                    $code = BaseBaseController::generarCodigo($tabla, 9);
                    $valorTotal = $this->valorTotalEmpresa();
                    $data = [
                        "code" => $code,
                        "customer_id" => $customer->id,
                        "company_id" => Auth::user()->company_id,
                        "customer_code" => $customer->code,
                        "afecta" => $transaction->afecta,
                        "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                        "customer_ruc" => $customer->numero_documento,
                        "customer_address" => $customer->direccion,
                        "customer_telefono" => $customer->telefono,
                        "type_transaction_id" => $transaction->id,
                        "type_transaction_name" => $transaction->name,
                        "type_transaction_action" => $transaction->action,
                        "valor_movimiento" => $pago->valor,
                        "saldo_general" => $valorTotal + $pago->valor,
                        "credit_folder_header_id" => $cabecera->id,
                        "credit_folder_details_id" => $cuota->id,
                        "observation" => 'PAGO DEL PRESTAMO ' . $cuota->code_folder_header . ', LA LETRA ' . $cuota->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos . '.',
                        "user_created_id" => Auth::user()->id,
                        "date_created" => date('Y-m-d'),
                        "hour_created" => date("H:i:s"),
                        "forma_pago_id" => $pago->forma_pago_id,
                        "forma_pago_name" => $pago->forma_pago,
                        "banco_id" => $this->esTransferencia($pago->forma_pago) ? ($pago->banco_id ?: null) : null,

                    ];

                    $movimientos = CustomerMovimiento::create($data);
                    $pago->customer_movimientos_id = $movimientos->id;
                    $pago->status = 1;
                    $pago->save();
                    $movimientosCreados[] = $movimientos->id;
                    CustomerCustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $pago->valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
                }
            } else { // caso solo pagos en efectivo
                foreach (
                    $pagos->get()->filter(function ($pago) {
                        return $this->esEfectivo($pago->forma_pago);
                    }) as $pago
                ) {

                    $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'PC')
                        ->firstOrFail();

                    $tabla = 'customer_movimientos';

                    $code = BaseBaseController::generarCodigo($tabla, 9);

                    $valorTotal = $this->valorTotalEmpresa();

                    $data = [
                        "code" => $code,
                        "customer_id" => $customer->id,
                        "company_id" => Auth::user()->company_id,
                        "customer_code" => $customer->code,
                        "afecta" => $transaction->afecta,
                        "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                        "customer_ruc" => $customer->numero_documento,
                        "customer_address" => $customer->direccion,
                        "customer_telefono" => $customer->telefono,
                        "type_transaction_id" => $transaction->id,
                        "type_transaction_name" => $transaction->name,
                        "type_transaction_action" => $transaction->action,
                        "valor_movimiento" => $pago->valor,
                        "saldo_general" => $valorTotal + $pago->valor,
                        "credit_folder_header_id" => $cabecera->id,
                        "credit_folder_details_id" => $cuota->id,
                        "observation" => 'PAGO DEL PRESTAMO ' . $cuota->code_folder_header .
                            ', LETRA ' . $cuota->numero_cuota .
                            '; CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos . '.',
                        "user_created_id" => Auth::user()->id,
                        "date_created" => date('Y-m-d'),
                        "hour_created" => date("H:i:s"),
                        "forma_pago_id" => $pago->forma_pago_id,
                        "forma_pago_name" => $pago->forma_pago,
                        "banco_id" => null,
                    ];

                    $movimientos = CustomerMovimiento::create($data);
                    $pago->customer_movimientos_id = $movimientos->id;
                    $pago->status = 1;
                    $pago->save();
                    $movimientosCreados[] = $movimientos->id;
                    CustomerCustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $pago->valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
                }
            }

            $etapa = 'crear los asientos contables';
            $this->contabilizarPagosAprobadosLetra($letraId, $movimientosCreados);

            $etapa = 'confirmar la transaccion';
            DB::commit();
            $this->dispatchBrowserEvent('alerta-pago-credito', ['titulo' => 'Éxito', 'mensaje' => 'Pago procesado correctamente', 'tipo' => 'success']);
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errorPago = 'No se pudo completar el pago de la cuota ' . $letraId
                . ' al ' . $etapa . '. Los cambios del pago se revirtieron.';
            \Illuminate\Support\Facades\Log::error('Fallo al pagar una cuota de credito.', [
                'letra_id' => $letraId,
                'etapa' => $etapa,
                'error' => $e->getMessage(),
            ]);
            report($e);
            if (!$this->errorContableNotificado) {
                $this->dispatchBrowserEvent(
                    'alerta-pago-credito',
                    ['titulo' => 'Error', 'mensaje' => $this->errorPago, 'tipo' => 'error']
                );
            }
            return false;
        }
    }

    private function valorTotalEmpresa()
    {
        $saldo = \App\Models\CustomerHistorial::where('company_id', Auth::user()->company_id)
            ->where('status', true)->whereNotNull('customer_movimiento_code')->where('customer_movimiento_code', '!=', '')
            ->sum(DB::raw("CASE WHEN type_transaction_action = 'S' THEN valor_movimiento WHEN type_transaction_action = 'R' THEN -valor_movimiento ELSE 0 END"));
        return max(0, $saldo);
    }
    private function contabilizarPagosAprobadosLetra($letraId, array $movimientoIds = []): void
    {
        $company = \App\Models\Company::find(Auth::user()->company_id);
        if (!$company || !$company->puedeContabilizar(date('Y-m-d'))) return;
        $movimientosLetra = CustomerMovimiento::where('company_id', Auth::user()->company_id)->where('credit_folder_details_id', $letraId)
            ->when(!empty($movimientoIds), function ($query) use ($movimientoIds) {
                $query->whereIn('id', $movimientoIds);
            })
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhereNotIn('status', [false, 0, '0']);
            })
            ->get();

        foreach ($movimientosLetra as $movimientoLetra) {
            $resultado = AsientosHeader::recalcularAsientos($movimientoLetra->id, 'customer_movimientos');

            if (($resultado['code'] ?? null) != '200') {

                $mensaje = $resultado['msg'] ?? 'No se pudo crear el asiento contable del pago.';
                $this->dispatchBrowserEvent('alerta-pago-credito', [
                    'titulo' => 'Error al crear el asiento contable',
                    'mensaje' => $mensaje . ' El pago no se completara y sus cambios se revertiran.',
                    'tipo' => 'error',
                ]);
                $this->errorContableNotificado = true;
                throw new \RuntimeException($mensaje);
            }
        }
    }


    // --- FUNCIONES DE SOPORTE ADAPTADAS ---

    private function procesarSobrante($codeHeader, $valor)
    {
        $SOBRANTE = $valor;
        while ($SOBRANTE > 0) {
            $proxima = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $codeHeader)
                ->where('status', 'PENDIENTE')
                ->where('saldo_anterior_cuota', 0)
                ->orderBy('numero_cuota')
                ->first();

            if (!$proxima) break;
            $SOBRANTE = $this->adelantarLetra($proxima->id, $SOBRANTE);
        }
    }

    private function procesarFaltante($codeHeader, $valor)
    {
        $proxima = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $codeHeader)
            ->where('status', 'PENDIENTE')
            ->where('faltante_anterior_cuota', 0)
            ->orderBy('numero_cuota')
            ->first();

        if ($proxima) {
            $this->faltanteLetra($proxima->id, $valor);
        }
    }

    private function manejarTransicionEstado($detalle): void
    {
        $header = \App\Models\CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('code', $detalle->code_folder_header)->first();
        if (!$header) {
            return;
        }

        // Ajustamos la ruta del modelo Prestamos aquí para evitar errores de importación
        $prestamo = \App\Models\Prestamos::find($header->tipo_prestamo);

        if ($header->status === 'ENPRUEBA' && $prestamo && !$prestamo->lleva_contabilidad) {
            $cuotas_pagadas = \App\Models\CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $header->code)
                ->where('numero_cuota', '<=', 3)
                ->where('status', 'PAGADA')
                ->count();
            if ($cuotas_pagadas >= 3) {
                $header->status = 'APROBADO';
                $header->save();
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => 'Felicidades',
                    'color' => 'success',
                    'mensaje' => 'El crédito ' . $header->code . ' pasó la fase de ENPRUEBA'
                ]);
            }
        }
    }

    private function adelantarLetra($id, $valor)
    {
        $detalle = $this->cuotaEmpresa($id, true);
        if ($valor >= $detalle->valor_cuota) {
            $SOBRANTE = (float) round($valor - $detalle->valor_cuota, 2);
            $abonado = $detalle->valor_cuota;
            $detalle->status = 'PAGADA';
        } else {
            $SOBRANTE = (float) round(0, 2);
            $abonado = $valor;
        }
        $detalle->saldo_anterior_cuota = $abonado;
        $detalle->save();
        return $SOBRANTE;
    }

    private function faltanteLetra($id, $valor)
    {
        $FALTANTE = (float) round($valor, 2);
        $detalle = $this->cuotaEmpresa($id, true);
        $detalle->faltante_anterior_cuota = $FALTANTE;
        $detalle->save();
    }

    private function ultimaLetraPago($id)
    {
        $detalle = $this->cuotaEmpresa($id, true);
        // Verifica si el remanente llegó a cero
        if ($detalle->saldo_remanente == "0.00") {
            $header = \App\Models\CreditFolderHeader::where('company_id', \Illuminate\Support\Facades\Auth::user()->company_id)
                ->where('code', $detalle->code_folder_header)
                ->first();

            // $header->status = "FINALIZADO"; // Comentado según tu código original
            if ($header) $header->save();
        }
    }

    public function render()
    {
        $letras = RegistroFormasPago::where('registro_formas_pagos.company_id', Auth::user()->company_id)->select(

            'letra_id',
            'prestamo_id',
            'customer_id',
            DB::raw("MIN(registro_formas_pagos.id) as id_orden"),
            DB::raw("(SELECT numero_cuota FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as numeroCuota"),
            DB::raw("(SELECT code_folder_header FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as codeFolderHeader"),
            DB::raw("MAX(solicitado) as estado_solicitud"),
            DB::raw("(SELECT nombres FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as nombres"),
            DB::raw("(SELECT apellidos FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as apellidos"),
            DB::raw("(SELECT numero_documento FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as numero_documento"),
            DB::raw("SUM(CASE WHEN UPPER(TRIM(forma_pago)) = 'TRANSFERENCIA' THEN valor ELSE 0 END) as valor_transferencia"),
            DB::raw("SUM(CASE WHEN UPPER(TRIM(forma_pago)) != 'TRANSFERENCIA' THEN valor ELSE 0 END) as valor_otros"),
            DB::raw("SUM(valor) as valor_total"),
            DB::raw("MAX(date_create) as fecha"),
            DB::raw("MAX(hour_create) as hora"),
            DB::raw("MAX(user_name) as usuario"),
            DB::raw("MAX(usuario_solicitud) as aprobado_por")
        )
            ->when($this->estadoFiltro !== '', function ($query) {
                $query->where('status', $this->estadoFiltro);
            })
            ->when($this->buscarCedula, function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw("(SELECT numero_documento FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) like ?", ["%{$this->buscarCedula}%"])
                        ->orWhereRaw("(SELECT nombres FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) like ?", ["%{$this->buscarCedula}%"])
                        ->orWhereRaw("(SELECT apellidos FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) like ?", ["%{$this->buscarCedula}%"]);
                });
            })
            ->groupBy('letra_id', 'prestamo_id', 'customer_id')
            ->havingRaw("SUM(CASE WHEN UPPER(TRIM(forma_pago)) = 'TRANSFERENCIA' THEN 1 ELSE 0 END) > 0")
            //->orderByDesc('letra_id')
            ->orderBy('id_orden', 'desc')
            ->paginate(15);

        return view('livewire.solicitud-pagos-creditos.solicitud-pagos-creditos', compact('letras'));
    }
}
