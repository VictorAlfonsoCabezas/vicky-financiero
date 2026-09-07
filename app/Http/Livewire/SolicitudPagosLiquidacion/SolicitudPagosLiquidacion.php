<?php

namespace App\Http\Livewire\SolicitudPagosLiquidacion;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\RegistroFormasLiquidacion;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CreditFolderDetail;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimiento;
use App\Models\Bancos;
use App\Http\Controllers\Customer\CustomerHistorialController;


use App\Http\Controllers\Base\BaseController;




class SolicitudPagosLiquidacion extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $buscarCedula = '';
    public $detallePagos = [];
    public $registro = null;
    public $liquidacionSeleccionada = null;
    public $observacionRechazo = '';
    public $mostrarFormularioRechazo = false;


    public function updatingBuscarCedula()
    {
        $this->resetPage();
    }


    public function cargarDatosLiquidacion(
        $liquidacionId,
        $fechaSolicitud = null,
        $horaSolicitud = null
    ) {
        $this->liquidacionSeleccionada = $liquidacionId;

        // Reiniciar formulario de rechazo
        $this->observacionRechazo = '';
        $this->mostrarFormularioRechazo = false;

        $this->registro = RegistroFormasLiquidacion::query()
            ->select(
                'registro_formas_liquidacion.*',
                'customer.nombres',
                'customer.apellidos',
                'customer.numero_documento',
                'credit_folder_headers.code as codeFolderHeader',
                'credit_folder_headers.valor_solicitado',
                'credit_folder_headers.total_pagando',
                'credit_folder_headers.cuotas_pagar'
            )
            ->leftJoin(
                'credit_folder_headers',
                'credit_folder_headers.id',
                '=',
                'registro_formas_liquidacion.liquidacion_id'
            )
            ->leftJoin(
                'customer',
                'customer.id',
                '=',
                'registro_formas_liquidacion.customer_id'
            )
            ->where(
                'registro_formas_liquidacion.liquidacion_id',
                $liquidacionId
            )
            ->when($fechaSolicitud, function ($query) use ($fechaSolicitud) {
                $query->where(
                    'registro_formas_liquidacion.fecha_solicitud',
                    $fechaSolicitud
                );
            })
            ->when($horaSolicitud, function ($query) use ($horaSolicitud) {
                $query->where(
                    'registro_formas_liquidacion.hora_solicitud',
                    $horaSolicitud
                );
            })
            ->orderBy(
                'registro_formas_liquidacion.id',
                'asc'
            )
            ->first();

        if (!$this->registro) {

            $this->detallePagos = [];

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información de la liquidación'
            ];

            $this->dispatchBrowserEvent('alerta', $data);

            return;
        }

        $detallePagos = RegistroFormasLiquidacion::with('banco')
            ->where(
                'liquidacion_id',
                $liquidacionId
            )
            ->when($fechaSolicitud, function ($query) use ($fechaSolicitud) {
                $query->where(
                    'fecha_solicitud',
                    $fechaSolicitud
                );
            })
            ->when($horaSolicitud, function ($query) use ($horaSolicitud) {
                $query->where(
                    'hora_solicitud',
                    $horaSolicitud
                );
            })
            ->orderBy('id', 'asc')
            ->get();

        $this->detallePagos = $detallePagos;

        $transferenciaRechazada = $detallePagos->first(function ($item) {
            return strtoupper(trim($item->forma_pago)) === 'TRANSFERENCIA'
                && $item->solicitado == 2;
        });

        if ($transferenciaRechazada) {
            $this->registro->observacion = $transferenciaRechazada->observacion;
        }
    }

    public function abrirFormularioRechazo()
    {
        $this->observacionRechazo = '';

        $this->mostrarFormularioRechazo = true;

        if ($this->liquidacionSeleccionada && $this->registro) {
            $this->cargarDatosLiquidacion(
                $this->liquidacionSeleccionada,
                $this->registro->fecha_solicitud,
                $this->registro->hora_solicitud
            );

            $this->mostrarFormularioRechazo = true;
        }
    }

    public function aprobarSolicitud(
        $liquidacionId,
        $fechaSolicitud,
        $horaSolicitud
    ) {
        $registros = RegistroFormasLiquidacion::where(
            'liquidacion_id',
            $liquidacionId
        )
            ->where(
                'fecha_solicitud',
                $fechaSolicitud
            )
            ->where(
                'hora_solicitud',
                $horaSolicitud
            )
            ->where(
                'solicitado',
                3
            )
            ->get();
        $pagosEfectuados =  $registros;


        if ($registros->isEmpty()) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No existe una solicitud de liquidación pendiente de aprobación'
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );

            return;
        }

        $header = CreditFolderHeader::find($liquidacionId);

        if (!$header) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del crédito'
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );

            return;
        }

        $customer = Customer::find(
            $header->customer_id
        );

        if (!$customer) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del cliente'
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );

            return;
        }

        $transaction = TypeTransaction::where(
            'company_id',
            Auth::user()->company_id
        )
            ->where(
                'name_corto',
                'LIC'
            )
            ->first();

        if (!$transaction) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la transacción de liquidación'
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );

            return;
        }

        $valorPagado = (float) round(
            $registros->sum('valor'),
            2
        );

        $transferencia = $registros->first(
            function ($registro) {

                return strtoupper(
                    trim($registro->forma_pago)
                ) === 'TRANSFERENCIA';
            }
        );

        DB::beginTransaction();

        try {

            $fechaActual = date('Y-m-d');
            $horaActual = date('H:i:s');

            /*
        |--------------------------------------------------------------------------
        | APROBAR SOLAMENTE LA SOLICITUD SELECCIONADA
        |--------------------------------------------------------------------------
        */

            foreach ($registros as $registro) {

                $registro->solicitado = 1;

                $registro->usuario_solicitud =
                    Auth::user()->username;

                $registro->usuario_id_solicitud =
                    Auth::user()->id;

                $registro->fecha_aprobacion =
                    $fechaActual;

                $registro->hora_aprobacion =
                    $horaActual;

                $registro->save();
            }

            /*
        |--------------------------------------------------------------------------
        | CALCULAR CUOTAS
        |--------------------------------------------------------------------------
        */

            if ($header->cuotas_pagar <= 12) {

                $cuotas = 6;
            } else {

                if (($header->cuotas_pagar % 2) == 0) {

                    $cuotas =
                        $header->cuotas_pagar / 2;
                } else {

                    $cuotas =
                        ((int) ($header->cuotas_pagar / 2)) + 1;
                }
            }

            /*
        |--------------------------------------------------------------------------
        | OBTENER CUOTAS PENDIENTES
        |--------------------------------------------------------------------------
        */

            $detalle = CreditFolderDetail::where(
                'company_id',
                Auth::user()->company_id
            )
                ->where(
                    'code_folder_header',
                    $header->code
                )
                ->where(
                    'status',
                    'PENDIENTE'
                )
                ->orderBy(
                    'id',
                    'asc'
                )
                ->get();

            if ($detalle->isEmpty()) {

                throw new \Exception(
                    'No existen cuotas pendientes para liquidar este crédito'
                );
            }

            $formasPagoNombres = $registros
                ->pluck('forma_pago')
                ->unique()
                ->implode(' + ');

            $ultimoDetalleId =
                $detalle->last()->id;

            /*
        |--------------------------------------------------------------------------
        | MARCAR CUOTAS COMO PAGADAS
        |--------------------------------------------------------------------------
        */

            foreach ($detalle as $det) {

                $capital =
                    $det->capital_amortizado;

                $desgravamen =
                    $det->fondo_desgravamen;

                if ($det->numero_cuota <= $cuotas) {

                    $interes =
                        $det->interes_periodo;
                } else {

                    $interes = 0;
                }

                $pagoRealizado =
                    (float) round(
                        (
                            $capital +
                            $desgravamen +
                            $interes
                        ),
                        2
                    );

                $det->valor_pagado =
                    $pagoRealizado;

                $det->valor_final =
                    $pagoRealizado;

                $det->tipo_pago =
                    $formasPagoNombres;

                if (
                    $transferencia &&
                    $det->id == $ultimoDetalleId
                ) {

                    $existeComprobante = CreditFolderDetail::where(
                        'company_id',
                        Auth::user()->company_id
                    )
                        ->where(
                            'banco_id',
                            $transferencia->banco_id
                        )
                        ->where(
                            'numero_comprobante',
                            $transferencia->numero_comprobante
                        )
                        ->where(
                            'id',
                            '!=',
                            $det->id
                        )
                        ->exists();

                    if ($existeComprobante) {

                        throw new \Exception(
                            'El número de comprobante ' .
                                $transferencia->numero_comprobante .
                                ' del banco seleccionado ya se encuentra registrado.'
                        );
                    }

                    $det->banco_id =
                        $transferencia->banco_id;

                    $det->numero_comprobante =
                        $transferencia->numero_comprobante;
                } else {

                    $det->banco_id = null;

                    $det->numero_comprobante = null;
                }

                $det->obervation_pago =
                    'PAGA LA TOTALIDAD DEL CREDITO ' .
                    $header->code .
                    ' ' .
                    $fechaActual;

                $det->date_pay =
                    $fechaActual;

                $det->hour_pay =
                    $horaActual;

                $det->user_pay_id =
                    Auth::user()->id;

                $det->status =
                    'PAGADA';

                $det->save();
            }

            /*
        |--------------------------------------------------------------------------
        | CREAR CUSTOMER MOVIMIENTO
        |--------------------------------------------------------------------------
        */
            foreach ($pagosEfectuados  as $pago) {
                $tabla = 'customer_movimientos';

                $code = BaseController::generarCodigo($tabla,  9);
                $valorTotal = BaseController::valorTotal();

                $dataMovimiento = [
                    'code' => $code,
                    'company_id' => Auth::user()->company_id,
                    'customer_code' => $customer->code,
                    'afecta' => $transaction->afecta,
                    'customer_name' => $customer->nombres . ' ' . $customer->apellidos,
                    'customer_ruc' => $customer->numero_documento,
                    'customer_address' => $customer->direccion,
                    'customer_telefono' => $customer->telefono,
                    'type_transaction_id' => $transaction->id,
                    'type_transaction_name' => $transaction->name,
                    'type_transaction_action' => $transaction->action,
                    'valor_movimiento' => $pago->valor,
                    'saldo_general' => $valorTotal + $pago->valor,
                    'observation' => 'PAGA LA TOTALIDAD DEL CREDITO ' .   $header->code . ' EN LA FECHA ' . $fechaActual,
                    'user_created_id' => Auth::user()->id,
                    'date_created' => $fechaActual,
                    'hour_created' => $horaActual,
                    'forma_pago_id' => $pago->forma_pago_id,
                    'forma_pago_name' => $pago->forma_pago,
                ];

                $movimientos = CustomerMovimiento::create($dataMovimiento);

                /*
                |--------------------------------------------------------------------------
                | HISTORIAL
                |--------------------------------------------------------------------------
                */

                CustomerHistorialController::guardarHistorialAutomatica(

                    $movimientos->code,

                    $transaction->id,

                    $pago->valor,

                    $customer->code,

                    $movimientos->saldo_general,

                    $fechaActual
                );


                /*
                |--------------------------------------------------------------------------
                | VINCULAR CUSTOMER_MOVIMIENTO
                | SOLO A LA SOLICITUD APROBADA
                |--------------------------------------------------------------------------
                */
                $regis = RegistroFormasLiquidacion::find($pago->id);
                $regis->customer_movimientos_id =  $movimientos->id;

                $regis->save();
            }


            /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CABECERA
        |--------------------------------------------------------------------------
        */

            $cabecera =  CreditFolderHeader::find($liquidacionId);
            $valornew = $cabecera->total_pagando + $valorPagado;
            $cabecera->total_pagando = $valornew;

            $cabecera->status = 'PAGADO';
            $cabecera->save();

            DB::commit();

            /*
        |--------------------------------------------------------------------------
        | LIMPIAR COMPONENTE
        |--------------------------------------------------------------------------
        */

            $this->registro = null;

            $this->detallePagos = [];

            $this->liquidacionSeleccionada =
                null;

            $this->observacionRechazo =
                '';

            $this->mostrarFormularioRechazo =
                false;

            $this->dispatchBrowserEvent(
                'close-modal-liquidacion'
            );

            $data = [

                'titulo' =>
                'Notificación',

                'color' =>
                'success',

                'mensaje' =>
                'La liquidación fue aprobada y procesada correctamente'
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            $data = [

                'titulo' =>
                'Notificación',

                'color' =>
                'danger',

                'mensaje' =>
                'Ocurrió un error al aprobar la liquidación: ' .
                    $e->getMessage()
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );
        }
    }

    public function rechazarSolicitud(
        $liquidacionId,
        $fechaSolicitud,
        $horaSolicitud
    ) {

        if (empty(trim($this->observacionRechazo))) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'Debe ingresar el motivo por el cual se rechaza la solicitud'
            ]);

            return;
        }

        $registros = RegistroFormasLiquidacion::where('liquidacion_id', $liquidacionId)
            ->where('fecha_solicitud', $fechaSolicitud)
            ->where('hora_solicitud', $horaSolicitud)
            ->where('solicitado', 3)
            ->get();

        $pagosEfectuados =  $registros;
       

        if ($registros->isEmpty()) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No existe una solicitud de liquidación pendiente de rechazo'
            ]);

            return;
        }

        $header = CreditFolderHeader::find($liquidacionId);

        if (!$header) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del crédito'
            ]);

            return;
        }

        $customer = Customer::find($header->customer_id);

        if (!$customer) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del cliente'
            ]);

            return;
        }

        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'LIC')
            ->first();

        if (!$transaction) {

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la transacción de liquidación'
            ]);

            return;
        }

        $registrosNoTransferencia = $registros->filter(function ($registro) {

            return strtoupper(trim($registro->forma_pago)) !== 'TRANSFERENCIA';
        });

        $valorPagado = round($registrosNoTransferencia->sum('valor'), 2);

        DB::beginTransaction();

        try {

            $fechaActual = date('Y-m-d');
            $horaActual = date('H:i:s');

            foreach ($registros as $registro) {

                $registro->usuario_solicitud = Auth::user()->username;
                $registro->usuario_id_solicitud = Auth::user()->id;
                $registro->fecha_aprobacion = $fechaActual;
                $registro->hora_aprobacion = $horaActual;

                if (strtoupper(trim($registro->forma_pago)) == 'TRANSFERENCIA') {

                    $registro->solicitado = 2;
                    $registro->observacion = 'TRANSFERENCIA RECHAZADA: ' . trim($this->observacionRechazo);
                } else {

                    $registro->solicitado = 1;
                }

                $registro->save();
            }

            if ($registrosNoTransferencia->isEmpty()) {

                DB::commit();

                $this->registro = null;
                $this->detallePagos = [];
                $this->liquidacionSeleccionada = null;
                $this->observacionRechazo = '';
                $this->mostrarFormularioRechazo = false;

                $this->dispatchBrowserEvent('close-modal-liquidacion');

                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => 'Notificación',
                    'color' => 'warning',
                    'mensaje' => 'La solicitud fue rechazada correctamente'
                ]);

                return;
            }

            $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
                ->where('code_folder_header', $header->code)
                ->where('status', 'PENDIENTE')
                ->orderBy('numero_cuota')
                ->get();

            if ($detalle->isEmpty()) {
                throw new \Exception('No existen cuotas pendientes para aplicar el pago');
            }

            $formasPago = $registrosNoTransferencia->pluck('forma_pago')->unique()->implode(' + ');
            $saldoPago = round($valorPagado, 2);
            foreach ($detalle as $det) {

                if ($saldoPago <= 0) {
                    break;
                }

                $valorCuota = round($det->valor_cuota, 2);
                $valorPendiente = round($valorCuota - $det->valor_pagado, 2);

                if ($valorPendiente <= 0) {
                    continue;
                }

                $abono = min($saldoPago, $valorPendiente);

                $det->valor_pagado = round($det->valor_pagado + $abono, 2);
                $det->valor_final = $det->valor_pagado;
                $det->tipo_pago = $formasPago;
                $det->date_pay = $fechaActual;
                $det->hour_pay = $horaActual;
                $det->user_pay_id = Auth::user()->id;
                $det->banco_id = null;
                $det->numero_comprobante = null;
                $det->obervation_pago = 'PAGO PARCIAL DEL CRÉDITO ' . $header->code . ' ' . $fechaActual;

                $faltante = round($valorCuota - $det->valor_pagado, 2);

                if ($faltante <= 0) {

                    $det->status = 'PAGADA';
                    $det->saldo_remanente = 0;
                    $det->faltante_anterior_cuota = 0;
                    $det->faltante_prox_cuota = 0;
                } else {
                    ///////




                    $det->status = 'PENDIENTE';
                    $det->saldo_remanente = $faltante;
                    $det->faltante_anterior_cuota = $faltante;
                    $det->faltante_prox_cuota = 0;
                }

                $det->save();

                $saldoPago = round($saldoPago - $abono, 2);
            }

            $header->total_pagando = round($header->total_pagando + $valorPagado, 2);

            if (round($header->total_pagando, 2) >= round($header->valor_solicitado, 2)) {
                $header->status = 'PAGADO';
            } else {
                $header->status = 'ENTREGADO';
            }

            $header->save();

            foreach ($registros as $registro) {
                if (strtoupper(trim($registro->forma_pago)) == 'TRANSFERENCIA') {
                } else {
                    $tabla = 'customer_movimientos';
                    $code = BaseController::generarCodigo($tabla, 9);
                    $valorTotal = BaseController::valorTotal();

                    $dataMovimiento = [
                        'code' => $code,
                        'company_id' => Auth::user()->company_id,
                        'customer_code' => $customer->code,
                        'afecta' => $transaction->afecta,
                        'customer_name' => $customer->nombres . ' ' . $customer->apellidos,
                        'customer_ruc' => $customer->numero_documento,
                        'customer_address' => $customer->direccion,
                        'customer_telefono' => $customer->telefono,
                        'type_transaction_id' => $transaction->id,
                        'type_transaction_name' => $transaction->name,
                        'type_transaction_action' => $transaction->action,
                        'valor_movimiento' => $registro->valor,
                        'saldo_general' => $valorTotal + $registro->valor,
                        'observation' => 'PAGO PARCIAL DEL CRÉDITO ' . $header->code . ' POR RECHAZO DE TRANSFERENCIA',
                        'user_created_id' => Auth::user()->id,
                        'date_created' => $fechaActual,
                        'hour_created' => $horaActual,
                        'forma_pago_id' => $registro->forma_pago_id,
                        'forma_pago_name' => $registro->forma_pago,
                    ];

                    $movimiento = CustomerMovimiento::create($dataMovimiento);
                    CustomerHistorialController::guardarHistorialAutomatica(
                        $movimiento->code,
                        $transaction->id,
                        $registro->valor,
                        $customer->code,
                        $movimiento->saldo_general,
                        $fechaActual
                    );

                    $regis = RegistroFormasLiquidacion::find($registro->id);
                    $regis->customer_movimientos_id =  $movimiento->id;

                    $regis->save();
                }
            }

            DB::commit();

            $this->registro = null;
            $this->detallePagos = [];
            $this->liquidacionSeleccionada = null;
            $this->observacionRechazo = '';
            $this->mostrarFormularioRechazo = false;

            $this->dispatchBrowserEvent('close-modal-liquidacion');

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'warning',
                'mensaje' => 'La transferencia fue rechazada. El pago recibido fue aplicado correctamente.'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'Ocurrió un error al rechazar la solicitud: ' . $e->getMessage()
            ]);
        }
    }


    public function render()
    {
        $liquidaciones = RegistroFormasLiquidacion::query()
            ->select(
                'registro_formas_liquidacion.liquidacion_id',
                'registro_formas_liquidacion.customer_id',
                'registro_formas_liquidacion.fecha_solicitud',
                'registro_formas_liquidacion.hora_solicitud',
                'customer.nombres',
                'customer.apellidos',
                'customer.numero_documento',
                'credit_folder_headers.code as codeFolderHeader',
                'registro_formas_liquidacion.user_name',
                'registro_formas_liquidacion.usuario_solicitud',

                DB::raw("
                SUM(
                    CASE
                        WHEN UPPER(TRIM(registro_formas_liquidacion.forma_pago)) = 'TRANSFERENCIA'
                        THEN registro_formas_liquidacion.valor
                        ELSE 0
                    END
                ) AS valor_transferencia
            "),

                DB::raw("
                SUM(
                    CASE
                        WHEN UPPER(TRIM(registro_formas_liquidacion.forma_pago)) <> 'TRANSFERENCIA'
                        THEN registro_formas_liquidacion.valor
                        ELSE 0
                    END
                ) AS valor_otros
            "),

                DB::raw("
                SUM(registro_formas_liquidacion.valor) AS valor_total
            "),

                DB::raw("
                MAX(
                    CASE
                        WHEN UPPER(TRIM(registro_formas_liquidacion.forma_pago)) = 'TRANSFERENCIA'
                        THEN registro_formas_liquidacion.solicitado
                        ELSE 0
                    END
                ) AS estado_solicitud
            ")
            )

            ->leftJoin(
                'credit_folder_headers',
                'credit_folder_headers.id',
                '=',
                'registro_formas_liquidacion.liquidacion_id'
            )

            ->leftJoin(
                'customer',
                'customer.id',
                '=',
                'registro_formas_liquidacion.customer_id'
            )

            ->whereIn(
                'registro_formas_liquidacion.solicitado',
                [1, 2, 3]
            )

            ->when($this->buscarCedula, function ($query) {

                $buscar = '%' . $this->buscarCedula . '%';

                $query->where(function ($q) use ($buscar) {

                    $q->where('customer.numero_documento', 'like', $buscar)
                        ->orWhere('customer.nombres', 'like', $buscar)
                        ->orWhere('customer.apellidos', 'like', $buscar);
                });
            })

            ->groupBy(
                'registro_formas_liquidacion.liquidacion_id',
                'registro_formas_liquidacion.customer_id',
                'registro_formas_liquidacion.fecha_solicitud',
                'registro_formas_liquidacion.hora_solicitud',
                'customer.nombres',
                'customer.apellidos',
                'customer.numero_documento',
                'credit_folder_headers.code',
                'registro_formas_liquidacion.user_name',
                'registro_formas_liquidacion.usuario_solicitud'
            )

            ->orderBy(
                'registro_formas_liquidacion.fecha_solicitud',
                'desc'
            )

            ->orderBy(
                'registro_formas_liquidacion.hora_solicitud',
                'desc'
            )

            ->paginate(10);

        return view(
            'livewire.solicitud-pagos-liquidacion.solicitud-pagos-liquidacion',
            compact('liquidaciones')
        );
    }
}
