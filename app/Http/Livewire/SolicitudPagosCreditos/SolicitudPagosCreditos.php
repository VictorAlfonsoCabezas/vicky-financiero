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


class SolicitudPagosCreditos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscarCedula = '';
    public $detallePagos = [];
    public $registro = null;
    public $letraSeleccionada;
    public $aplicarInteresMora = true;
    public $interesMoraOriginal = 0;

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

    public function cargarDatosPago($letraId, $esCargaInicial = false)
    {
        $this->letraSeleccionada = $letraId;


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
            ->where('registro_formas_pagos.letra_id', $letraId)
            ->orderBy('registro_formas_pagos.id')
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
            ->where('letra_id', $letraId)
            ->first();

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
        $this->totalAprobar = $this->totalRecibido;

        if (!$this->aplicarInteresMora) {

            $this->totalAprobar -= $this->interesMoraOriginal;

            if ($this->totalAprobar < 0) {
                $this->totalAprobar = 0;
            }
        }
    }

    public function updatedAplicarInteresMora()
    {
        if ($this->letraSeleccionada) {
            // Ejecutamos tu función para mantener los datos del cliente frescos
            $this->cargarDatosPago($this->letraSeleccionada);
        } else {
            $this->actualizarTotalAprobar();
        }
    }

    public function aprobarSolicitud($letraId)
    {
        // Buscamos los registros de pago pendientes para esta letra
        $pagos = RegistroFormasPago::where('letra_id', $letraId)
            ->where('status', 3)
            ->get();

        if ($pagos->isEmpty()) {
            return;
        }

        $total = $pagos->sum('valor');

        if (!$this->aplicarInteresMora) {
            $total = round($total - $this->interesMoraOriginal, 2);
        }

        RegistroFormasPago::where('letra_id', $letraId)->update([
            'solicitado' => 1,
            'usuario_solicitud' => auth()->user()->username,
            'usuario_id_solicitud' => auth()->user()->id,
            'fecha_solicitud' => date('Y-m-d'),
            'hora_solicitud' => date('H:i:s')
        ]);

        $this->pagarLetra($total, $letraId, true);

        // Aseguramos que la letra quede como PAGADA
        CreditFolderDetail::where('id', $letraId)->update([
            'status' => 'PAGADA'
        ]);

        $this->cargarDatosPago($letraId);

        $this->reset([
            'registro',
            'detallePagos',
            'letraSeleccionada',
            'aplicarInteresMora',
        ]);

        $this->aplicarInteresMora = true;

        $this->dispatchBrowserEvent('close-modal-pagos');
    }

    public function rechazarSolicitud($letraId)
    {
        $pagos = RegistroFormasPago::where('letra_id', $letraId)
            ->where('status', 3)
            ->get();

        if ($pagos->isEmpty()) {
            return;
        }

        $pagosEfectivo = $pagos->filter(function ($pago) {
            return $this->esEfectivo($pago->forma_pago);
        });

        $pagosRechazados = $pagos->reject(function ($pago) {
            return $this->esEfectivo($pago->forma_pago);
        });

        // Al rechazar, solo el efectivo queda aprobado para contabilidad.
        foreach ($pagosRechazados as $pago) {

            $pago->solicitado = 2;
            $pago->status = 2;
            $pago->usuario_solicitud = auth()->user()->username;
            $pago->usuario_id_solicitud = auth()->user()->id;
            $pago->fecha_solicitud = date('Y-m-d');
            $pago->hora_solicitud = date('H:i:s');

            $pago->save();
        }

        if ($pagosEfectivo->count() == 0) {

            $cuota = CreditFolderDetail::find($letraId);

            if ($cuota) {

                $cuota->status = 'PENDIENTE';
                $cuota->tipo_pago = 'EFECTIVO';

                $cuota->valor_pagado = 0;
                $cuota->valor_final = 0;
                $cuota->date_pay = null;
                $cuota->hour_pay = null;
                $cuota->user_pay_id = null;

                $cuota->save();
            }

            $this->emit('alerta', [
                'titulo' => 'Información',
                'mensaje' => 'La transferencia fue rechazada y la cuota volvió a estado PENDIENTE.',
                'color' => 'warning'
            ]);

            $this->cargarDatosPago($letraId);

            $this->reset([
                'registro',
                'detallePagos',
                'letraSeleccionada',
                'aplicarInteresMora',
            ]);

            $this->aplicarInteresMora = true;

            $this->dispatchBrowserEvent('close-modal-pagos');

            return;
        }

        foreach ($pagosEfectivo as $pago) {

            $pago->solicitado = 1;
            $pago->usuario_solicitud = auth()->user()->username;
            $pago->usuario_id_solicitud = auth()->user()->id;
            $pago->fecha_solicitud = date('Y-m-d');
            $pago->hora_solicitud = date('H:i:s');

            $pago->save();
        }

        $total = $pagosEfectivo->sum('valor');

        $this->pagarLetra($total, $letraId, false);
        $this->cargarDatosPago($letraId);
        $this->reset([
            'registro',
            'detallePagos',
            'letraSeleccionada',
            'aplicarInteresMora',
        ]);

        $this->aplicarInteresMora = true;

        $this->dispatchBrowserEvent('close-modal-pagos');
    }

    /*public function rechazarSolicitud($letraId)
    {
        if (empty(trim($this->observacionRechazo))) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'Debe ingresar el motivo por el cual se rechaza la solicitud.'
            ]);

            return;
        }

        $pagos = RegistroFormasPago::where('letra_id', $letraId)
            ->where('status', 3)
            ->get();

        if ($pagos->isEmpty()) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No existe una solicitud pendiente.'
            ]);

            return;
        }

        DB::beginTransaction();

        try {

            $fechaActual = date('Y-m-d');
            $horaActual = date('H:i:s');

            $pagosEfectivo = $pagos->filter(function ($pago) {
                return $this->esEfectivo($pago->forma_pago);
            });

            $pagosRechazados = $pagos->reject(function ($pago) {
                return $this->esEfectivo($pago->forma_pago);
            });

            foreach ($pagosRechazados as $pago) {

                $pago->solicitado = 2;
                $pago->status = 2;
                $pago->usuario_solicitud = Auth::user()->username;
                $pago->usuario_id_solicitud = Auth::user()->id;
                $pago->fecha_solicitud = $fechaActual;
                $pago->hora_solicitud = $horaActual;
                $pago->observacion = 'PAGO RECHAZADO: ' . trim($this->observacionRechazo);

                $pago->save();
            }

            if ($pagosEfectivo->isEmpty()) {

                $cuota = CreditFolderDetail::find($letraId);

                if ($cuota) {

                    $cuota->status = 'PENDIENTE';
                    $cuota->tipo_pago = 'EFECTIVO';
                    $cuota->valor_pagado = 0;
                    $cuota->valor_final = 0;
                    $cuota->date_pay = null;
                    $cuota->hour_pay = null;
                    $cuota->user_pay_id = null;
                    $cuota->save();
                }

                DB::commit();

                $this->registro = null;
                $this->detallePagos = [];
                $this->letraSeleccionada = null;
                $this->observacionRechazo = '';
                $this->aplicarInteresMora = true;

                $this->dispatchBrowserEvent('close-modal-pagos');

                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => 'Notificación',
                    'color' => 'warning',
                    'mensaje' => 'La solicitud fue rechazada correctamente.'
                ]);

                return;
            }

            foreach ($pagosEfectivo as $pago) {

                $pago->solicitado = 1;
                $pago->usuario_solicitud = Auth::user()->username;
                $pago->usuario_id_solicitud = Auth::user()->id;
                $pago->fecha_solicitud = $fechaActual;
                $pago->hora_solicitud = $horaActual;

                $pago->save();
            }

            $total = round($pagosEfectivo->sum('valor'), 2);

            $this->pagarLetra($total, $letraId, false);

            DB::commit();

            $this->registro = null;
            $this->detallePagos = [];
            $this->letraSeleccionada = null;
            $this->observacionRechazo = '';
            $this->aplicarInteresMora = true;

            $this->dispatchBrowserEvent('close-modal-pagos');

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'warning',
                'mensaje' => 'La transferencia fue rechazada y el pago recibido fue aplicado correctamente.'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'Ocurrió un error al rechazar la solicitud: ' . $e->getMessage()
            ]);
        }
    }*/

    public function pagarLetra($valorPagar, $letraId, $incluyeTransferencia = true)
    {
        $cuota = CreditFolderDetail::find($letraId);

        $cuota->interes_mora = $this->aplicarInteresMora
            ? $this->interesMoraOriginal
            : 0;

        $cuota->save();

        $interesMora = $cuota->interes_mora ?? 0; // Usamos el valor que ya tenga la cuota

        // Lógica de cálculo idéntica a tu realizarPago()
        $pagoTotalReq = (float) round(($cuota->valor_cuota + $interesMora + $cuota->faltante_anterior_cuota - $cuota->saldo_anterior_cuota), 2);

        // Determinamos el CASO
        $caso = 1;
        if ($valorPagar > $pagoTotalReq) $caso = 2;
        if ($valorPagar < $pagoTotalReq) $caso = 3;

        DB::beginTransaction();
        try {
            // Actualización de la cuota principal
            $cuota->valor_pagado = $valorPagar;
            $cuota->valor_final = $valorPagar;
            $cuota->date_pay = date('Y-m-d');
            $cuota->hour_pay = date('H:i:s');
            $cuota->user_pay_id = Auth::user()->id;
            $cuota->status = 'PAGADA';

            if ($caso == 2) {
                $SOBRANTE = (float) round($valorPagar - $pagoTotalReq, 2);
                $cuota->adelanto_prox_cuota = $SOBRANTE;

                $this->procesarSobrante($cuota->code_folder_header, $SOBRANTE);
            }

            if ($caso == 3) {
                $FALTANTE = (float) round($pagoTotalReq - $valorPagar, 2);
                $cuota->faltante_prox_cuota = $FALTANTE;

                $this->procesarFaltante($cuota->code_folder_header, $FALTANTE);
            }

            $cuota->save();

            // Ejecutamos tus funciones de transición y cierre
            $this->manejarTransicionEstado($cuota);
            $this->ultimaLetraPago($letraId);

            // Actualizar Cabecera
            $cabecera = CreditFolderHeader::where('code', $cuota->code_folder_header)->first();
            $cabecera->total_pagando += $valorPagar;
            $cabecera->save();

            $customer = Customer::find($cabecera->customer_id);

            $pagos = RegistroFormasPago::where('letra_id', $letraId)
                ->where('status', 3);
            $movimientosCreados = [];

            if ($incluyeTransferencia) { // caso todos los pagos incluidos
                foreach ($pagos->get() as $pago) {

                    $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                        ->where('name_corto', 'PC')
                        ->first();
                    $tabla = 'customer_movimientos';
                    $code = BaseBaseController::generarCodigo($tabla, 9);
                    $valorTotal = BaseBaseController::valorTotal();
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
                        ->first();

                    $tabla = 'customer_movimientos';

                    $code = BaseBaseController::generarCodigo($tabla, 9);

                    $valorTotal = BaseBaseController::valorTotal();

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

            $this->contabilizarPagosAprobadosLetra($letraId, $movimientosCreados);

            DB::commit();
            $this->emit('alerta', ['titulo' => 'Éxito', 'mensaje' => 'Pago procesado correctamente', 'color' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('alerta', ['titulo' => 'Error', 'mensaje' => $e->getMessage(), 'color' => 'danger']);
        }
    }

    private function contabilizarPagosAprobadosLetra($letraId, array $movimientoIds = []): void
    {
        $movimientosLetra = CustomerMovimiento::where('credit_folder_details_id', $letraId)
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
                throw new \Exception($resultado['msg'] ?? 'No se pudo crear el asiento contable del pago.');
            }
        }
    }

    // --- FUNCIONES DE SOPORTE ADAPTADAS ---

    private function procesarSobrante($codeHeader, $valor)
    {
        $SOBRANTE = $valor;
        while ($SOBRANTE > 0) {
            $proxima = CreditFolderDetail::where('code_folder_header', $codeHeader)
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
        $proxima = CreditFolderDetail::where('code_folder_header', $codeHeader)
            ->where('status', 'PENDIENTE')
            ->where('faltante_anterior_cuota', 0)
            ->orderBy('numero_cuota')
            ->first();

        if ($proxima) {
            $this->faltanteLetra($proxima->id, $valor);
        }
    }

    public function manejarTransicionEstado($detalle): void
    {
        $header = \App\Models\CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        if (!$header) {
            return;
        }

        // Ajustamos la ruta del modelo Prestamos aquí para evitar errores de importación
        $prestamo = \App\Models\Prestamos::find($header->tipo_prestamo);

        if ($header->status === 'ENPRUEBA' && $prestamo && !$prestamo->lleva_contabilidad) {
            $cuotas_pagadas = \App\Models\CreditFolderDetail::where('code_folder_header', $header->code)
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

    public function adelantarLetra($id, $valor)
    {
        $detalle = \App\Models\CreditFolderDetail::find($id);
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

    public function faltanteLetra($id, $valor)
    {
        $FALTANTE = (float) round($valor, 2);
        $detalle = \App\Models\CreditFolderDetail::find($id);
        $detalle->faltante_anterior_cuota = $FALTANTE;
        $detalle->save();
    }

    public function ultimaLetraPago($id)
    {
        $detalle = \App\Models\CreditFolderDetail::find($id);
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
        $registroRender = null;
        $detallePagosRender = [];

        // Si hay una letra seleccionada, cargamos los datos frescos para la vista
        if ($this->letraSeleccionada) {
            $detallePagosRender = RegistroFormasPago::select('registro_formas_pagos.*', 'credit_folder_details.path')
                ->leftJoin('credit_folder_details', 'credit_folder_details.id', '=', 'registro_formas_pagos.letra_id')
                ->where('registro_formas_pagos.letra_id', $this->letraSeleccionada)
                ->orderBy('registro_formas_pagos.id')
                ->get();

            $registroRender = RegistroFormasPago::select(
                'registro_formas_pagos.*',
                DB::raw("(SELECT numero_cuota FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as numeroCuota"),
                DB::raw("(SELECT code_folder_header FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as codeFolderHeader"),
                DB::raw("(SELECT interes_mora FROM credit_folder_details WHERE credit_folder_details.id = registro_formas_pagos.letra_id LIMIT 1) as interes_mora"),
                DB::raw("(SELECT nombres FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as nombres"),
                DB::raw("(SELECT apellidos FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as apellidos"),
                DB::raw("(SELECT numero_documento FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) as numero_documento")
            )
                ->where('letra_id', $this->letraSeleccionada)
                ->first();
        }

        $letras = RegistroFormasPago::select(

            'letra_id',
            'prestamo_id',
            'numero_comprobante',
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
            ->where('status', 3)
            ->when($this->buscarCedula, function ($query) {
                $query->where(function ($q) {
                    $q->whereRaw("(SELECT numero_documento FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) like ?", ["%{$this->buscarCedula}%"])
                        ->orWhereRaw("(SELECT nombres FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) like ?", ["%{$this->buscarCedula}%"])
                        ->orWhereRaw("(SELECT apellidos FROM customer WHERE customer.id = registro_formas_pagos.customer_id LIMIT 1) like ?", ["%{$this->buscarCedula}%"]);
                });
            })
            ->groupBy('letra_id', 'prestamo_id')
            ->havingRaw("SUM(CASE WHEN UPPER(TRIM(forma_pago)) = 'TRANSFERENCIA' THEN 1 ELSE 0 END) > 0")
            //->orderByDesc('letra_id')
            ->orderBy('id_orden', 'desc')
            ->paginate(15);

        return view('livewire.solicitud-pagos-creditos.solicitud-pagos-creditos', compact('letras', 'registroRender', 'detallePagosRender'));
    }
}
