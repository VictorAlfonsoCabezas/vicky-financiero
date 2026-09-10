<?php

namespace App\Http\Livewire\Cuentas;

use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Cartola\CartolaController;
use App\Http\Controllers\Cuentas\CuentasController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Bancos;
use App\Models\Bovedas;
use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Models\CustomerMovimientoSolicitud;
use App\Models\CustomerTipoAhorros;
use App\Models\FormasPago;
use App\Models\TipoAhorros;
use App\Models\TipoAhorrosDetalle;
use App\Models\TipoAhorrosProgramadosDetalle;
use App\Models\TypeTransaction;
use App\Models\WhaEnvios;
use App\Models\Cajas;
use App\Models\HeaderCalculadora;
use App\Models\CustomerMovimientoDetalleCalculadora;
use App\Models\CartolaHeader;
use App\Models\Company;
use App\Models\Country;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\DescargoBovedasHeader;
use App\Models\EntregaEncajes;
use App\Models\OperacionesDescargoBovedas;
use App\Models\TipoCuenta;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PDF;


//MODIFICACIÓN

use Dompdf\Dompdf;
use Dompdf\Options;

use Illuminate\Support\Facades\Response;

class CuentasComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $archivo;
    public $descrpcion = '';
    public $search = '';
    public $customer_selec = 0;
    public $cuenta_selec = 0;
    public $valor_transaccion = '';
    public $pagoTipoBanco = false;
    public $comprobante = '';
    public $banco_id = '';
    public $numero_deposito = '';
    public $forma_pago_id = '';
    public $valor_transaccion_calculadora = '';
    public $observacion_transaccion = '';
    public $envio_whatsapp = true;
    public $nota_debito = false;
    public $tipo_transaccion = 'IN';
    public $tipoAhorros;
    public $formasPago;
    public $selectedTipoAhorro;
    public $whatsapp;
    public $fechaWhatsapp;
    public $certificados = 0;
    public $programado = 0;
    public $detalleValores = [];
    public $sumaValores = 0;
    public $primerAhorroValores = 0;
    public $tipoAhorroColor = 0;
    public $tipoAhorroCuenta = '';
    public $numeroCuenta = '';
    public $interes = [];
    public $cuenta_valor = '';
    public $tipo_ahorros_programados_detalle_id = 0;
    public $cuenta_pago = '';
    public $diasManual = '';
    //usarManual modificado de '' a false
    public $usarManual = false;
    public $activarManual = false;
    public $tipoAhorrosPrgramadosDetalleId = null;
    public $plazoDiasProgramado = null;
    public $customerMovimientoCreateID = null;
    public $validarCaja = false;
    public $numeroNewCartola = '';
    public $numeroEditCartola = '';
    public $listaCartolas = [];
    public $numeroCartolaActiva = '';
    public $cumplimiento = false;
    public $detallePlazoFijo = [];
    public $cuentaPagarPlazo = [];
    public $cuentaClienteAhorro = [];

    public $cuentaPagarPlazoLista = '';
    public $cuentasCliente = '';
    public $cuentaMaestraPlazoFijo = '';
    public $pagarMaestra = false;
    public $seleccionPlazoPagar = false;
    public $historialCuenrtas = [];
    public $encajesEntregados = [];
    public $valorCalculadoPrestamoFijo;

    public $cuentaPagarPlazoListaNovar = '';
    public $valor_acreditar_retirar = 0;
    public $valor_total_acreditar_retirar = 0;

    public $tipo_ahorros_programados_detalle_id_novacion = 0;
    public $cuenta_pago_novacion = '';
    public $usarManualNovacion = '';
    public $activarManualNovacion = false;
    public $diasManualNovacion = '';
    public $beneficiarioProgramado = '';
    public $beneficiarioProgramadoNovar = '';
    public $retirarValores = true;
    public $retirarValoresEncaje = false;

    public $fechaRetiroEncaje = '';
    public $horaRetiroEncaje = '';
    public $ValorDepositoEncaje = '';
    public $ValorRetenidoEncaje = '';
    public $ValorEntregarEncaje = '';
    public $porcentajeRetencion = '';

    public $porcentaje_plazo_fijo = '';
    public $valor_plazo_fijo = '';


    //MODIFICACIÓN

    public function generarPdfContratoApertura()
    {
        $company = Company::first();
        $cliente = Customer::find($this->customer_selec);

        if (!$cliente) {
            abort(404, 'Cliente no seleccionado');
        }

        // MARCA DE AGUA

        if ($company->photo != null && $company->photo != '') {

            $path = 'uploads/companies/' . $company->photo;

            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }

        $man = file_get_contents(public_path($path));

        $imagen = base64_encode($man);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('pdf.contrato-apertura', [
            'company' => $company,
            'cliente' => $cliente,
            'imagen' => $imagen
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombreArchivo = 'Contrato_Apertura_' . preg_replace('/[^A-Za-z0-9_]/', '_', $cliente->apellidos . '_' . $cliente->nombres) . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $nombreArchivo);
    }

    public function imprimirTicket($id)
    {
        $data['movimiento'] = CustomerMovimiento::find($id);
        $data['cuenta'] = CustomerTipoAhorros::find($data['movimiento']->customer_tipo_ahorro_id);
        $data['tipoCuenta'] = TipoAhorros::find($data['cuenta']->tipo_ahorros_id);

        $data['usuario'] = User::find($data['movimiento']->user_created_id);
        $data['formaPago'] = 'EFECTIVO';
        $data['caja'] = 'CARGA INICIAL';
        $caja = Cajas::where('user_inicial_id', $data['movimiento']->user_created_id)
            ->where('date_inicial', $data['movimiento']->date_created)
            ->first();
        if ($caja) {
            $apertura = OperacionesDescargoBovedas::where('nombre_corto', 'CAJINI')->first();
            $descargoBovedaHeader = DescargoBovedasHeader::where('cajas_id', $id)
                ->where('estado', 'FINALIZADO')
                ->where('operaciones_descargo_bovedas_id',  $apertura->id)
                ->where('cajas_id',  $caja->id)
                ->first();
            if ($descargoBovedaHeader) {
                $boveda = Bovedas::find($descargoBovedaHeader->boveda_origen_id);
                $data['caja'] = $boveda->nombre;
            }
        }
        $formaPago = FormasPago::find($data['movimiento']->forma_pago_id);
        if ($formaPago) {
            $data['formaPago'] = $formaPago->nombre;
        }
        // Crear PDF con tamaño de ticket (95mm de ancho)

        $pdf = PDF::loadView('ticket.transacciones', $data)->setPaper([0, 0, 269.29, 360], 'portrait');

        // Convertir en base64 para enviarlo a la vista
        $pdfBase64 = base64_encode($pdf->output());

        // Emitir evento a JavaScript
        $this->dispatchBrowserEvent('mostrar-pdf', ['pdf' => $pdfBase64]);
    }
    public function seleccionarCliente($id)
    {
        $this->customer_selec = $id;
        $this->cuenta_selec = 0;
    }

    public function abrirModalTipo()
    {
        $this->tipoAhorroColor = 0;
        $this->tipoAhorroCuenta = '';
        $this->cuenta_valor = '';
        $this->tipo_ahorros_programados_detalle_id = 0;
        $this->cuenta_pago = '';
        $this->selectedTipoAhorro = null;
        $this->usarManual = '';
        $this->activarManual = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function llenarChat($id)
    {
        $this->whatsapp = '';
        $this->fechaWhatsapp = '';
        $movimientos = CustomerMovimiento::find($id);
        if (
            WhaEnvios::where('company_id', Auth::user()->company_id)
            ->where('proviene_id', $id)
            ->where('type_transacction_id', $movimientos->type_transaction_id)
            // ->where('type_transacction_name', $movimientos->type_transacction_name)
            ->where('customer_id', $this->customer_selec)
            ->count() > 0
        ) {
            $message = WhaEnvios::where('company_id', Auth::user()->company_id)
                ->where('type_transacction_id', $movimientos->type_transaction_id)
                // ->where('type_transaction_name', $movimientos->type_transacction_name)
                ->where('customer_id', $this->customer_selec)
                ->where('proviene_id', $id)
                ->first();
            $this->whatsapp = $message->whatsapp;
            $this->fechaWhatsapp = $message->fecha_envio;
        }
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function tipoTransaccion($tipo)
    {
        $this->tipo_transaccion = $tipo;
        $this->primerAhorroValores = 0;
        if ($tipo == 'IN') {
            $cliente = Customer::find($this->customer_selec);
            $saldoCuenta = $this->saldoEnCuenta($cliente, $this->cuenta_selec);
            if ($saldoCuenta == 0) {
                $cuenta = CustomerTipoAhorros::find($this->cuenta_selec);
                $sumaValores = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->sum('valor');
                if ($sumaValores !== 0) {
                    $this->primerAhorroValores = 1;
                    $detalle = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->get()->toArray();
                    $this->detalleValores = $detalle;
                    $this->sumaValores = $sumaValores;
                }
            }
        }
        $this->valor_transaccion = '';
        $this->observacion_transaccion = '';
        $this->comprobante = '';
        $this->banco_id = 1;
        $this->numero_deposito = '';
        $this->forma_pago_id = 1;
        $this->envio_whatsapp = true;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function eliminarCuenta($id)
    {
        $movimientos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
            ->where('customer_tipo_ahorro_id', $id)
            ->get();
        if ($movimientos->count() == 0) {
            CustomerTipoAhorros::find($id)->delete();
            $color = 'success';
            $mensaje = 'La cuenta se ha eliminado correctamente';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
        } else {
            $color = 'danger';
            $mensaje = 'La cuenta registra movimientos, por lo que no se puede eliminar';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];
        }
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function cuentaSeleccionada($id)
    {
        $this->retirarValores = true;
        $this->retirarValoresEncaje = false;
        $this->selectedTipoAhorro = 0;
        $this->numeroCartolaActiva = '';
        $this->listaCartolas = [];
        $this->cuenta_selec = $id;
        $cuenta = CustomerTipoAhorros::find($id);
        $tipoCuenta = TipoAhorros::find($cuenta->tipo_ahorros_id);
        if ($tipoCuenta->ahorro_prestamo) {
            $prestamosCodes = CreditFolderHeader::where('customer_id', $cuenta->customer_id)
                ->pluck('code');

            $letrasInpagas = CreditFolderDetail::whereIn('code_folder_header', $prestamosCodes)
                ->where('status', 'PENDIENTE')
                ->count();
            if ($letrasInpagas > 0) {
                $this->retirarValores = false;
            }
        } else if ($tipoCuenta->cuenta_encaje) {
            $this->retirarValores = false;
            $this->retirarValoresEncaje = true;
        }
        $this->certificados = $cuenta->tipoAhorros->cuenta_certificado;
        $this->programado = $cuenta->tipoAhorros->programado;
        $this->primerAhorroValores = 0;
        $this->cumplimiento = $cuenta->cumplimiento;
        $cartolaActiva = CartolaHeader::where('customer_tipo_ahorro_id', $id)
            ->where('customer_id', $this->customer_selec)
            ->where('status', 'ACTIVA')
            ->first();
        if ($cartolaActiva != null) {
            $this->numeroCartolaActiva = $cartolaActiva->numero;
        }
        $this->consultarCartolas();
    }

    function cambioRenovacion()
    {
        $cuenta = CustomerTipoAhorros::find($this->cuenta_selec);
        $cuenta->cumplimiento = ($cuenta->cumplimiento) ? false : true;
        $cuenta->save();
        $this->cuentaSeleccionada($this->cuenta_selec);
    }

    public function storeCuenta()
    {
        $tipoAhorros = TipoAhorros::find($this->selectedTipoAhorro);
        //$formaPago = FormasPago::find($this->forma_pago_id);

        $this->validate(
            [
                'selectedTipoAhorro' => 'required',
                'numeroCuenta' => [
                    'required',
                    'digits:10',
                    Rule::unique('customer_tipo_ahorros', 'codigo')
                ],
            ],
            [
                'selectedTipoAhorro.required' => 'Necesita seleccionar un tipo de cuenta',
                'numeroCuenta.required' => 'El Número de cuenta es Obligatorio',
                'numeroCuenta.unique' => 'El Número de cuenta ya existe en el sistema',
                'numeroCuenta.digits' => 'El Número de cuenta debe tener exactamente 10 dígitos',
            ]
        );

        if ($tipoAhorros->programado) {

            $cajaAbierta = Cajas::where('status', 'ABIERTA')
                ->where('date_inicial', date('Y-m-d'))
                ->where('user_inicial_id', Auth::user()->id)
                ->count();
            if ($cajaAbierta == 1) {
                $this->validarCaja = true;
            } else {
                $this->validarCaja = false;
            }
            if ($this->validarCaja == false) {
                $color = 'danger';
                $mensaje = 'No se puede realizar esta operacion hasta que tenga una caja abierta';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;
            }

            $this->validate(
                [
                    'cuenta_valor' => 'required',
                    'tipo_ahorros_programados_detalle_id' => 'required',
                    'cuenta_pago' => 'required',
                    'diasManual' => ($this->usarManual == true) ? 'required' : '',
                ],

                [
                    'cuenta_valor.required' => 'Necesita ingresar un valor.',
                    'tipo_ahorros_programados_detalle_id.required' => 'Necesita seleccionar un porcentaje.',
                    'cuenta_pago.required' => 'Necesita seleccionar la forma de desembolso.',
                    'diasManual.required' => 'Usted selecciono ingresar el plazo manual este campo es obligatorio.',
                ]

            );

            $formaPago = FormasPago::find($this->forma_pago_id);

            if (
                $formaPago &&
                (
                    trim($formaPago->nombre) == 'TRANSFERENCIA' ||
                    trim($formaPago->nombre) == 'CHEQUE'
                )
            ) {

                $this->validate(
                    [
                        'comprobante' => 'required',
                        'banco_id' => 'required',
                        'numero_deposito' => 'required',
                    ],
                    [
                        'comprobante.required' => 'Necesita ingresar el número de comprobante.',
                        'banco_id.required' => 'Necesita seleccionar un banco.',
                        'numero_deposito.required' => 'Necesita ingresar el número de depósito.',
                    ]
                );
            }
        }

        $nuevoCodigo = $this->numeroCuenta;
        $newCuenta = new CustomerTipoAhorros();
        $newCuenta->company_id = Auth::user()->company_id;
        $newCuenta->customer_id = $this->customer_selec;
        $newCuenta->tipo_ahorros_id = $this->selectedTipoAhorro;
        $newCuenta->codigo = $nuevoCodigo;
        $newCuenta->valor = $this->cuenta_valor;
        $newCuenta->tipo_ahorros_programados_detalle_id = $this->tipo_ahorros_programados_detalle_id;
        $newCuenta->pago = $this->cuenta_pago;
        $newCuenta->forma_pago_id = $this->forma_pago_id;
        $newCuenta->comprobante = $this->comprobante;
        $newCuenta->banco_id = $this->banco_id;
        $newCuenta->numero_deposito = $this->numero_deposito;

        //$newCuenta->status = true;
        $formaPago = FormasPago::find($this->forma_pago_id);

        if (
            $formaPago &&
            (
                trim($formaPago->nombre) == 'TRANSFERENCIA' ||
                trim($formaPago->nombre) == 'CHEQUE'
            )
        ) {
            $newCuenta->status = 0;
        } else {
            $newCuenta->status = 1;
        }
        $newCuenta->save();

        //        inicio acreditacion automatica
        $this->valor_transaccion_calculadora = null;
        $this->plazoDiasProgramado = null;
        $this->tipoAhorrosPrgramadosDetalleId = null;
        if ($tipoAhorros->programado) {
            $this->tipo_transaccion = 'DFJ';
            $this->cuenta_selec = $newCuenta->id;
            $this->valor_transaccion = $this->cuenta_valor;
            $this->observacion_transaccion = 'ACREDITACIÓN DE VALORES POR PLAZO FIJO';
            $this->tipoAhorrosPrgramadosDetalleId = $this->tipo_ahorros_programados_detalle_id;
            $tipoAhorrosProgramados = TipoAhorrosProgramadosDetalle::find($newCuenta->tipo_ahorros_programados_detalle_id);
            if ($this->diasManual) {
                $this->plazoDiasProgramado = $this->diasManual;
            } else {
                $this->plazoDiasProgramado = $tipoAhorrosProgramados->rango_max;
            }
            $calculo = ($this->cuenta_valor) * ($tipoAhorrosProgramados->interes / 100);
            $penalizado = $calculo * (2 / 100);
            $diasAlCalculo = $tipoAhorrosProgramados->rango_max;
            $this->valor_transaccion_calculadora = number_format($this->cuenta_valor + $calculo - $penalizado);
            if ($this->diasManual) {
                $diasAlCalculo = $this->diasManual;
            }
            $fecha = \Carbon\Carbon::parse(date('Y-m-d'));
            $fechaMasDias = $fecha->addDays($diasAlCalculo);
            $fechaPago = $fechaMasDias->toDateString();
            $vieneAhorroProgramadoONovacion = true;
            $this->storeAhorroRetiro($vieneAhorroProgramadoONovacion);
            $entero = intdiv($diasAlCalculo, 30);
            $calculoReal = (($calculo / 12) * $entero);
            $company = Company::find(Auth::user()->company_id);
            $penalizadoReal = 0;
            if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                //$penalizado = $this->valorGanadoVer * (2 / 100);
                $penalizadoReal = $calculoReal * (2 / 100);
            }
            $movimientosCustomer = CustomerMovimiento::find($this->customerMovimientoCreateID);
            $tipoAhorroProgramadoDetalle = TipoAhorrosProgramadosDetalle::find($movimientosCustomer->tipo_ahorros_programados_detalle_id);
            $dataHeader = [
                'company_id' => Auth::user()->company_id,
                'customer_id' => $movimientosCustomer->customer_id,
                'beneficiarioProgramado' => $this->beneficiarioProgramado,
                'customer_movimientos_id' => $movimientosCustomer->id,
                'tipo_pago' => $this->cuenta_pago,
                'tipo_ahorros_programados_detalle_id' => $movimientosCustomer->tipo_ahorros_programados_detalle_id,
                'interes' => $tipoAhorroProgramadoDetalle->interes,
                'dias_plazo' => $movimientosCustomer->plazo_dias_programado,
                'rentabilidad' => $calculoReal,
                'penalizado' => $penalizadoReal,
                'total' => $calculoReal - $penalizadoReal,
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $headerCalculadora = HeaderCalculadora::create($dataHeader);
            if ($this->cuenta_pago != 'C') {
                $data = array();
                $ganado = number_format($calculoReal / $entero, 2);
                $penali = 0;
                if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                    //$penali = number_format($penalizado / $entero, 2);
                    $penali = number_format($penalizadoReal / $entero, 2);
                }
                $fechaPago = date('Y-m-d');
                for ($i = 1; $i <= $entero; $i++) {
                    $fecha = \Carbon\Carbon::parse($fechaPago);
                    $fechaMasDias = $fecha->addMonths(1);
                    $fechaPago = $fechaMasDias->toDateString();
                    $data = [
                        'header_calculadora_id' => $headerCalculadora->id,
                        'customer_movimientos_id' => $this->customerMovimientoCreateID,
                        'rentabilidad' => $ganado,
                        'penalizado' => $penali,
                        'total' => ($ganado - $penali),
                        'fecha_pago' => $fechaPago,
                    ];
                    CustomerMovimientoDetalleCalculadora::create($data);
                }
                $this->mensualizados = $data;
            } else {
                $data = [
                    'header_calculadora_id' => $headerCalculadora->id,
                    'customer_movimientos_id' => $this->customerMovimientoCreateID,
                    'rentabilidad' => $calculoReal,
                    'penalizado' => $penalizadoReal,
                    'total' => ($calculoReal - $penalizadoReal),
                    'fecha_pago' => $fechaPago,
                ];
                CustomerMovimientoDetalleCalculadora::create($data);
            }
            $cunetaClinete = CustomerTipoAhorros::find($newCuenta->id);
            $cunetaClinete->dias_plazo = $this->plazoDiasProgramado;
            $cunetaClinete->save();
        }
        //        fin acreditacion automatica
        $this->dispatchBrowserEvent('closeModal');
    }

    public function onDiasManualChange()
    {
        if ($this->diasManual < 30) {
            $color = 'danger';
            $mensaje = 'El valor no puede ser inferior a 30 días, se colocara automáticamente el valor mínimo.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->diasManual = 30;
            return;
        }
    }
    public function onDiasManualChangeNovacion()
    {

        if ($this->diasManualNovacion < 30) {
            $color = 'danger';
            $mensaje = 'El valor no puede ser inferior a 30 días, se colocara automáticamente el valor mínimo.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->diasManualNovacion = 30;
            return;
        }
    }

    public function storeAhorroRetiro($vieneAhorroProgramadoONovacion = false)
    {
        $tipoAhorrosValidar = TipoAhorros::find($this->selectedTipoAhorro);
        $cuentaOperacion = CustomerTipoAhorros::find($this->cuenta_selec);
        $esDepositoCertificado = $this->tipo_transaccion === 'IN'
            && $cuentaOperacion
            && $cuentaOperacion->tipoAhorros
            && (bool) $cuentaOperacion->tipoAhorros->cuenta_certificado;


        if (!isset($tipoAhorrosValidar->programado)) {

            $this->validate(
                [
                    'valor_transaccion' => 'required',
                    //'comprobante' => 'required',
                    'observacion_transaccion' => 'required',
                ],
                [
                    'valor_transaccion.required' => 'Debe colocar un valor.',
                    //'comprobante.required' => 'Debe colocar un numero de comprobante.',
                    'observacion_transaccion.required' => 'Debe ingresar una observación.',
                ]
            );
        } else if ($tipoAhorrosValidar->programado == false) {
            $this->validate(
                [
                    'valor_transaccion' => 'required',
                    //'comprobante' => 'required',
                    'observacion_transaccion' => 'required',
                ],
                [
                    'valor_transaccion.required' => 'Debe colocar un valor.',
                    //'comprobante.required' => 'Debe colocar un numero de comprobante.',
                    'observacion_transaccion.required' => 'Debe ingresar una observación.',
                ]
            );
        }


        $cajaAbierta = Cajas::where('status', 'ABIERTA')
            ->where('date_inicial', date('Y-m-d'))
            ->where('user_inicial_id', Auth::user()->id)
            ->count();
        if ($cajaAbierta == 1) {
            $this->validarCaja = true;
        } else {
            $this->validarCaja = false;
        }
        if ($this->validarCaja == false) {
            $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Sin Caja Abierta</b>';
            $mensaje = 'No se puede realizar esta operación hasta que tenga una caja abierta. <a href="/cajas" autofocus>Ir a cajas</a>';
            $this->dispatchBrowserEvent('alertaGrande', [
                'titulo' => $titulo,
                'color' => 'danger',
                'mensaje' => $mensaje
            ]);
            return;
        }
        $this->validate([
            "valor_transaccion" => ['required', 'regex:/^\d+(\.\d{1,2})?$/', 'numeric', 'min:0'],
        ]);

        //Validar si tiene saldo suficiente
        if ($this->tipo_transaccion == 'EG') {
            $cliente = Customer::find($this->customer_selec);
            //verificar si tiene valores 
            $cuenta = CustomerTipoAhorros::find($this->cuenta_selec);
            $sumaValores = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->sum('valor');
            $retenido = 0;
            if ($sumaValores !== 0) {
                $retenido = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->where('bloqueado', true)->sum('valor');
            }
            $saldo = $this->saldoEnCuenta($cliente, $this->cuenta_selec);
            $saldoFinal = $saldo - $retenido;
            $valorTransaccion = $this->valor_transaccion;
            if ($valorTransaccion > $saldoFinal) {
                $titulo = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia';
                $mensaje = "El valor que intenta retirar es:<b> {$this->valor_transaccion}$ </b> y su saldo en esta cuenta es:<b> {$saldo}$</b>";
                $this->dispatchBrowserEvent('alertaGrande', [
                    'titulo' => $titulo,
                    'color' => 'danger',
                    'mensaje' => $mensaje
                ]);
                return;
            }
        }

        //Validar si caja dispone del dinero suficiente para desembolsar
        if ($this->nota_debito == false && $this->tipo_transaccion == 'EG') {
            $cajaUsuario = Cajas::where('status', 'ABIERTA')
                ->where('date_inicial', date('Y-m-d'))
                ->where('user_inicial_id', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->first();

            if (isset($cajaUsuario->id)) {
                $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'CAJCIE')->first();
                $valoresBoveda = DescargoBovedasHeader::where('cajas_id', $cajaUsuario->id)
                    ->where('estado', 'FINALIZADO')
                    ->where('operaciones_descargo_bovedas_id', '!=', $operacion->id)
                    ->sum('valor');

                $formaPagoCredito =  0;
                $formaPago =  FormasPago::where('nombre', 'TRANSFERENCIA')->first();
                if ($formaPago) {
                    $formaPagoCredito = $formaPago->id;
                }

                $notaDebito = TypeTransaction::where('name_corto', 'DND')->first();
                $notaCredito = TypeTransaction::where('name_corto', 'DNC')->first();

                $historialEgreso = CustomerHistorial::join('customer_movimientos', 'customer_movimientos.code', '=', 'customer_historials.customer_movimiento_code')
                    ->where('customer_historials.type_transaction_action', 'R')
                    ->whereNotNull('customer_historials.customer_movimiento_code')
                    ->where('customer_historials.customer_movimiento_code', '!=', '')
                    ->where('customer_historials.date_created', $cajaUsuario->date_inicial)
                    ->where('customer_historials.status', true)
                    ->where('customer_historials.type_transaction_id', '!=', $notaDebito->id)
                    ->where('customer_movimientos.user_created_id', Auth::user()->id)
                    ->where(function ($query) use ($formaPagoCredito) {
                        $query->where('customer_movimientos.forma_pago_id', '!=', $formaPagoCredito)
                            ->orWhereNull('customer_movimientos.forma_pago_id');
                    })
                    ->sum('customer_historials.valor_movimiento');

                $historialIngreso = CustomerHistorial::join('customer_movimientos', 'customer_movimientos.code', '=', 'customer_historials.customer_movimiento_code')
                    ->where('customer_historials.type_transaction_action', 'S')
                    ->whereNotNull('customer_historials.customer_movimiento_code')
                    ->where('customer_historials.customer_movimiento_code', '!=', '')
                    ->where('customer_historials.date_created',  $cajaUsuario->date_inicial)
                    ->where('customer_historials.status', true)
                    ->where('customer_historials.type_transaction_id', '!=', $notaCredito->id)
                    ->where('customer_movimientos.user_created_id', Auth::user()->id)
                    ->where(function ($query) use ($formaPagoCredito) {
                        $query->where('customer_movimientos.forma_pago_id', '!=', $formaPagoCredito)
                            ->orWhereNull('customer_movimientos.forma_pago_id');
                    })
                    ->sum('customer_historials.valor_movimiento');

                $valorTransaccion = floatval($this->valor_transaccion);
                $saldoBoveda = floatval($valoresBoveda + $historialIngreso - $historialEgreso);
                if ($valorTransaccion > $saldoBoveda) {
                    $this->dispatchBrowserEvent('alertaGrande', [
                        'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i><b>Advertencia</b>',
                        'color' => 'danger',
                        'mensaje' => "El valor que intenta retirar es:<b> {$this->valor_transaccion}$ </b> y su saldo en CAJA es:<b> {$saldoBoveda}$</b> , por favor <b>Solicitar a su caja desde su Bóveda.</b>"
                    ]);
                    return;
                }
            }
        }
        //Validar cuenta certificado excede el monto
        if ($this->tipo_transaccion == 'IN') {
            $customerTipoAhorro = CustomerTipoAhorros::find($this->cuenta_selec);
            $cliente = Customer::find($this->customer_selec);
            $saldoCuenta = $this->saldoEnCuenta($cliente, $this->cuenta_selec);
            $saldo = $saldoCuenta + $this->valor_transaccion;
            if ($customerTipoAhorro->tipoAhorros->cuenta_certificado && $saldo > $customerTipoAhorro->tipoAhorros->cuenta_certificado_valor_max) {
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => '<i class="fa fa-lock" aria-hidden="true"></i> Advertencia',
                    'color' => 'warning',
                    'mensaje' => "<b>Cuenta de Certificados:</b> la transacción que ingresa es <b> {$this->valor_transaccion} $</b> y sumado a su saldo:<b> {$saldoCuenta} $</b> exceden al valor máximo de aportación <b>{$customerTipoAhorro->tipoAhorros->cuenta_certificado_valor_max} $</b>"
                ]);
                return;
            }
        }

        //Validar cuenta certificados tiene valores definidos a pagar
        if ($this->tipo_transaccion == 'IN') {
            $customerTipoAhorro = CustomerTipoAhorros::find($this->cuenta_selec);
            $cliente = Customer::find($this->customer_selec);
            $saldoCuenta = $this->saldoEnCuenta($cliente, $this->cuenta_selec);
            $saldo = $saldoCuenta + $this->valor_transaccion;
            if ($customerTipoAhorro->tipoAhorros->cuenta_certificado && $saldo > $customerTipoAhorro->tipoAhorros->cuenta_certificado_valor_max) {
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => '<i class="fa fa-lock" aria-hidden="true"></i> Advertencia',
                    'color' => 'warning',
                    'mensaje' => "<b>Cuenta de Certificados:</b> la transacción que ingresa es <b> {$this->valor_transaccion} $</b> y sumado a su saldo:<b> {$saldoCuenta} $</b> exceden al valor máximo de aportación <b>{$customerTipoAhorro->tipoAhorros->cuenta_certificado_valor_max} $</b>"
                ]);
                return;
            }
        }

        //Validar si la cuenta tiene un valor inicial
        $dividirPagos = false;
        $cuenta = CustomerTipoAhorros::find($this->cuenta_selec);
        $sumaValores = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->sum('valor');
        if ($this->tipo_transaccion == 'IN' && ($this->saldoEnCuenta($cliente, $this->cuenta_selec)) == 0 && $sumaValores != 0) {
            $customerTipoAhorro = CustomerTipoAhorros::find($this->cuenta_selec);
            $cliente = Customer::find($this->customer_selec);
            $saldoCuenta = $this->saldoEnCuenta($cliente, $this->cuenta_selec);
            $saldo = $saldoCuenta + $this->valor_transaccion;


            if ($sumaValores != $saldo) {
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => '<i class="fa fa-lock" aria-hidden="true"></i> Advertencia',
                    'color' => 'warning',
                    'mensaje' => "<b>Cuenta de con valores iniciales: </b> la transacción que ingresa es <b> {$this->valor_transaccion} $</b> y lo que se debe ingresar es:<b> {$sumaValores} $</b>"
                ]);
                return;
            } else {
                $dividirPagos = true;
            }
        }

        // Validar si Nota crédito o Nota de Débito
        if (!$vieneAhorroProgramadoONovacion && intval($this->forma_pago_id) !== 1) { // FP 1 es efectivo
            $this->validate(
                [
                    'banco_id' => 'required',
                    'numero_deposito' => 'required',
                ],
                [
                    'banco_id.required' => 'El banco es obligatorio.',
                    'numero_deposito.required' => 'El #número de depósito es obligatorio.',
                ]
            );

            // Cambiamos la transacción Notas credito y debito
            if ($this->tipo_transaccion == 'IN') {
                $this->tipo_transaccion = $esDepositoCertificado ? 'APS' : 'DNC';
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => '<i class="fa fa-lock" aria-hidden="true"></i> Advertencia',
                    'color' => 'warning',
                    'mensaje' => "<b>Nota de Crédito:</b> Esta operación, requiere aprobación previa antes de ingresar como uma transaccion.</b>"
                ]);

                // Enviar a aprobación
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', $this->tipo_transaccion)
                    ->first();

                $solicitud = new CustomerMovimientoSolicitud();
                $solicitud->company_id = Auth::user()->company_id;
                $solicitud->user_id = Auth::user()->id;
                $solicitud->banco_id = $this->banco_id;
                $solicitud->comprobante = $this->comprobante;
                $solicitud->numero_deposito = $this->numero_deposito;
                $solicitud->forma_pago_id = $this->forma_pago_id;
                $solicitud->type_transaction_id = $transaction->id;
                $solicitud->customer_id = $cuenta->customer_id;
                $solicitud->fecha_creacion = date('Y-m-d H:i:s');
                $solicitud->customer_tipo_ahorro_id = $cuenta->id;
                $solicitud->valor = $this->valor_transaccion;
                $solicitud->observacion = $this->observacion_transaccion;
                $solicitud->save();

                $this->dispatchBrowserEvent('closeModal');
                return;
            }
        }

        // Validamos que sea nota de debito
        if (!$vieneAhorroProgramadoONovacion && $this->nota_debito && $this->tipo_transaccion == 'EG') {
            $this->tipo_transaccion = 'DND';
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => '<i class="fa fa-lock" aria-hidden="true"></i> Advertencia',
                'color' => 'warning',
                'mensaje' => "<b>Nota de Débito:</b> Esta es una operacion interna el cual debita valor de la cuenta.</b>"
            ]);
        }

        if (!$dividirPagos) {
            $customer = Customer::find($this->customer_selec);
            $tipoTransaccionMovimiento = $esDepositoCertificado ? 'APS' : $this->tipo_transaccion;
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', $tipoTransaccionMovimiento)
                ->first();
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "comprobante" => $this->comprobante,
                // Nuevos
                "banco_id" => ($this->forma_pago_id !== 1) ? $this->banco_id : null,
                "numero_deposito" => ($this->forma_pago_id !== 1) ? $this->numero_deposito : null,
                "forma_pago_id" => isset($this->forma_pago_id) ? $this->forma_pago_id : null,
                "forma_pago_name" => isset(FormasPago::find($this->forma_pago_id)->nombre) ? FormasPago::find($this->forma_pago_id)->nombre : null,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "customer_tipo_ahorro_id" => $this->cuenta_selec,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $this->valor_transaccion,
                "saldo_general" => $valorTotal + $this->valor_transaccion,
                "observation" => ($this->observacion_transaccion != '') ? $this->observacion_transaccion : (($transaction->action == 'S') ? 'SE ACREDITO $' . $this->valor_transaccion . ' AL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos : 'SE RETIRO $' . $this->valor_transaccion . ' DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos),
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
                "valor_calculadora" => $this->valor_transaccion_calculadora,
                "plazo_dias_programado" => $this->plazoDiasProgramado,
                "tipo_ahorros_programados_detalle_id" => $this->tipoAhorrosPrgramadosDetalleId,
            ];
            $movimientos = CustomerMovimiento::create($data);
            $this->customerMovimientoCreateID = $movimientos->id;
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->valor_transaccion, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            //CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento, date('Y-m-d'));
            BaseController::guardarValoresCartola($this->cuenta_selec, $customer->id, $movimientos, $transaction);
            BaseController::enviarMail($movimientos);
            if ($this->envio_whatsapp) {
                $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $this->valor_transaccion . '$* Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;
                $whatsapp = new WhaEnvios();
                $whatsapp->company_id = Auth::user()->company_id;
                $whatsapp->envio_ahora = true;
                $whatsapp->es_transacion = true;
                $whatsapp->inmediato = true;
                $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
                $whatsapp->type_transacction_id = $transaction->id;
                $whatsapp->type_transacction_name = $transaction->name;
                $whatsapp->proviene_id = $movimientos->id;
                $whatsapp->customer_id = $customer->id;
                $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
                $whatsapp->customer_celular = $customer->telefono;
                $whatsapp->whatsapp = $whatsappEnviar;
                $whatsapp->estado = 'PENDIENTE';
                $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
                $whatsapp->fecha_envio = date('Y-m-d H:i:s');
                $whatsapp->save();
            }

            $this->dispatchBrowserEvent('closeModal');
            if ($this->tipo_transaccion == 'IN') {
                $color = 'success';
                $mensaje = 'El ahorro se realizó correctamente';
            } else {
                $color = 'danger';
                $mensaje = 'El retiro se realizó correctamente';
            }
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        } else {
            $cuenta = CustomerTipoAhorros::find($this->cuenta_selec);
            $detalle = TipoAhorrosDetalle::where('tipo_ahorros_id', $cuenta->tipoAhorros->id)->get();
            $customer = Customer::find($this->customer_selec);
            foreach ($detalle as $key => $value) {
                if ($esDepositoCertificado) {
                    $transaccion = 'APS';
                } else if ($value->afecta == 'E') {
                    $transaccion = 'SE';
                } else {
                    $transaccion = 'SC';
                }
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', $transaccion)
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $data = [
                    "code" => $code,
                    "comprobante" => $this->comprobante,
                    // Nuevos
                    "banco_id" => ($this->forma_pago_id !== 1) ? $this->banco_id : null,
                    "numero_deposito" => ($this->forma_pago_id !== 1) ? $this->numero_deposito : null,
                    "forma_pago_id" => isset($this->forma_pago_id) ? $this->forma_pago_id : null,
                    "forma_pago_name" => isset(FormasPago::find($this->forma_pago_id)->nombre) ? FormasPago::find($this->forma_pago_id)->nombre : null,
                    "company_id" => Auth::user()->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "afecta" => $transaction->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "customer_tipo_ahorro_id" => $this->cuenta_selec,
                    "tipo_ahorro_detalle_id" => $value->id,
                    "type_transaction_id" => $transaction->id,
                    "type_transaction_name" => $transaction->name,
                    "type_transaction_action" => $transaction->action,
                    "valor_movimiento" => $value->valor,
                    "saldo_general" => $valorTotal + $value->valor,
                    "observation" => $this->observacion_transaccion,
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),
                ];
                $movimientos = CustomerMovimiento::create($data);
                $this->customerMovimientoCreateID = $movimientos->id;
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $value->valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
                BaseController::guardarValoresCartola($this->cuenta_selec, $customer->id, $movimientos, $transaction);
                BaseController::enviarMail($movimientos);
                if ($this->envio_whatsapp && $value->afecta == 'E') {
                    $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $this->valor_transaccion . '$* Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;
                    $whatsapp = new WhaEnvios();
                    $whatsapp->company_id = Auth::user()->company_id;
                    $whatsapp->envio_ahora = true;
                    $whatsapp->es_transacion = true;
                    $whatsapp->inmediato = true;
                    $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
                    $whatsapp->type_transacction_id = $transaction->id;
                    $whatsapp->type_transacction_name = $transaction->name;
                    $whatsapp->proviene_id = $movimientos->id;
                    $whatsapp->customer_id = $customer->id;
                    $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
                    $whatsapp->customer_celular = $customer->telefono;
                    $whatsapp->whatsapp = $whatsappEnviar;
                    $whatsapp->estado = 'PENDIENTE';
                    $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
                    $whatsapp->fecha_envio = date('Y-m-d H:i:s');
                    $whatsapp->save();
                }
            }
            $this->customerMovimientoCreateID = $movimientos->id;
            $this->dispatchBrowserEvent('closeModal');
            $color = 'success';
            $mensaje = 'Transaccion realizada con éxito';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
    }

    public function saldoEnCuenta($cliente, $cuenta)
    {
        $cliente = Customer::find($this->customer_selec);
        $ingresos = CustomerHistorial::where('customer_code', $cliente->code)
            ->where('type_transaction_action', 'S')
            // ->where('type_transaction_name', 'INGRESOS')
            ->where('customer_tipo_ahorro_id', $cuenta)
            ->where('status', true)
            ->sum('valor_movimiento');
        $egresos = CustomerHistorial::where('customer_code', $cliente->code)
            ->where('type_transaction_action', 'R')
            // ->where('type_transaction_name', 'EGRESOS')
            ->where('customer_tipo_ahorro_id', $cuenta)
            ->where('status', true)
            ->sum('valor_movimiento');
        $valor = $ingresos - $egresos;
        $saldoCuenta = number_format(round($valor, 2), 2, '.', '');
        return $saldoCuenta;
    }

    public function convertirFecha($fecha)
    {
        $carbonFecha = Carbon::createFromFormat('Y-m-d', $fecha);
        $carbonFecha->locale('es');
        $fechaFormateada = $carbonFecha->isoFormat('dddd, D [de] MMMM [del] YYYY');
        return $fechaFormateada;
    }


    public function subirArchivo()
    {
        $this->validate([
            'archivo' => 'required|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:20240',
        ]);
        $ruta = $this->archivo->store('public/clientes');
        $extension = $this->archivo->getClientOriginalExtension();
        $url = Storage::url($ruta);
        $customerFile = new CustomerFile();
        $customerFile->company_id = Auth::user()->company_id;
        $customerFile->customer_id = $this->customer_selec;
        $customerFile->customer_tipo_ahorros_id = $this->cuenta_selec;
        $customerFile->descripcion = $this->descrpcion ?? 'Archivo sin descripción';
        $customerFile->formato = $extension;
        $customerFile->path = $url;
        $customerFile->archivo = $ruta;
        $customerFile->save();
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'success',
            'mensaje' => 'Se guardó correctamente'
        ]);
    }

    private function limpiarFormulario()
    {
        $this->reset(['archivo', 'descrpcion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function eliminarFile($id)
    {
        $customerFile = CustomerFile::find($id);
        if ($customerFile->archivo) {
            Storage::delete($customerFile->archivo);
        }
        $customerFile->delete();
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'danger',
            'mensaje' => 'Se eliminó correctamente'
        ]);
    }

    public function cambioTipoAhorro($id)
    {
        $tipoAhorro = TipoAhorros::find($id);
        $this->tipoAhorroColor = $tipoAhorro->class;
        $this->numeroCuenta = CuentasController::buscarNumeroCuenta();
        if ($tipoAhorro->programado) {
            $this->tipoAhorroCuenta = "PROGRAMADO";
        } else if ($tipoAhorro->cuenta_certificado) {
            $this->tipoAhorroCuenta = "CERTIFICADO";
        } else {
            $this->tipoAhorroCuenta = "GENERAL";
        }
    }

    public function cambioFormaPago($id)
    {
        $formaPago = FormasPago::find($id);
        $this->pagoTipoBanco = false;
        if (trim($formaPago->nombre) == 'TRANSFERENCIA' || trim($formaPago->nombre) == 'CHEQUE') {
            $this->pagoTipoBanco = true;
        }
    }

    public function updatedFormaPagoId($id)
    {
        $this->cambioFormaPagoAhorroProgramado($id);
    }

    public function cambioFormaPagoAhorroProgramado($id)
    {
        $formaPago = FormasPago::find($id);

        $this->pagoTipoBanco = false;

        if (!$formaPago) {
            return;
        }

        if (
            trim($formaPago->nombre) == 'TRANSFERENCIA' ||
            trim($formaPago->nombre) == 'CHEQUE'
        ) {
            $this->pagoTipoBanco = true;
        } else {

            $this->comprobante = '';
            $this->banco_id = '';
            $this->numero_deposito = '';
        }
    }

    public function toggleUsarManual()
    {
        if ($this->usarManual) {
            $this->activarManual = true;
        } else {
            $this->activarManual = false;
        }
    }
    public function toggleUsarManualNovacion()
    {
        if ($this->usarManualNovacion) {
            $this->diasManualNovacion = 30;
            $this->activarManualNovacion = true;
        } else {
            $this->diasManualNovacion = '';
            $this->activarManualNovacion = false;
        }
    }

    public function envioWhatsapp($id)
    {
        //Buscar telefono desde cliente
        $movimiento = CustomerMovimiento::find($id);

        $customer = Customer::find($movimiento->customer_id);

        $country = Country::find($customer->country_id);
        if ($country->codigo_pais == '593') {
            $celular = '593' . substr($movimiento->customer->telefono, 1);
        } else {
            $celular = $country->codigo_pais  . $movimiento->customer->telefono;
        }

        $transaction = TypeTransaction::find($movimiento->type_transaction_id);
        $customer = Customer::find($movimiento->customer_id);

        // Restructurar Mensaje
        $mensaje = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $movimiento->valor_movimiento . '$* Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;

        $whatsapp = WhaEnvios::where('proviene_id', $id)->first();
        if (is_null($whatsapp)) {
            $whatsapp = new WhaEnvios();
            $whatsapp->company_id = Auth::user()->company_id;
            $whatsapp->envio_ahora = true;
            $whatsapp->es_transacion = true;
            $whatsapp->inmediato = true;
            $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
            $whatsapp->type_transacction_id = $transaction->id;
            $whatsapp->type_transacction_name = $transaction->name;
            $whatsapp->proviene_id = $id;
            $whatsapp->customer_id = $customer->id;
            $whatsapp->customer_name = $customer->nombres . ' ' . $customer->apellidos;
            $whatsapp->customer_celular = $customer->telefono;
            $whatsapp->whatsapp = $mensaje;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->save();
        } else {
            $whatsapp->whatsapp = $mensaje;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->save();
        }

        $data = [
            'code' => '200',
            'telefono' => $celular,
            'mensaje' => $mensaje,
        ];
        $this->dispatchBrowserEvent('whatsapp', $data);
    }

    public function consultarCartolas()
    {
        $this->numeroNewCartola = '';
        $cartolas = CartolaHeader::where('customer_tipo_ahorro_id', $this->cuenta_selec)->get();
        $this->listaCartolas = $cartolas;
    }
    public function cambioEstadoCartola($id)
    {
        $cartola = CartolaHeader::find($id);
        $cartola->status = ($cartola->status == 'ACTIVA') ? 'CERRADA' : 'ACTIVA';
        $cartola->save();
        $this->consultarCartolas();
    }
    public function cambiarValorCartola($id, $nuevoValor)
    {
        $cartola = CartolaHeader::find($id);
        $cartola->numero = $nuevoValor;
        $cartola->save();
        $this->cuentaSeleccionada($this->cuenta_selec);
    }
    public function guardarNuevaCartola()
    {
        $this->validate(
            [
                'numeroNewCartola' => 'required',
            ],
            [
                'numeroNewCartola.required' => 'Necesita ingresar de cartola.',
            ]
        );
        $cartolasTodas = CartolaHeader::where('customer_tipo_ahorro_id', $this->cuenta_selec)
            ->where('status', 'ACTIVA')
            ->get();
        foreach ($cartolasTodas as $car) {
            $cartola = CartolaHeader::find($car->id);
            $cartola->status = 'CERRADA';
            $cartola->save();
        }
        $tabla = 'cartola_headers';
        $code = BaseController::generarCodigoCron($tabla, 3);
        $cliente = Customer::find($this->customer_selec);
        $cartolaNew = [
            "code" => $code,
            "numero" => $this->numeroNewCartola,
            "company_id" => Auth::user()->company_id,
            "customer_id" => $cliente->id,
            "customer_code" => $cliente->code,
            "customer_name" => $cliente->nombres . ' ' . $cliente->apellidos,
            "customer_date_create" => date('Y-m-d'),
            "status" => 'ACTIVA',
            "date_create" => date('Y-m-d'),
            "hour_create" => date('H:i:s'),
            "customer_tipo_ahorro_id" => $this->cuenta_selec,
        ];
        $cartolanueva = CartolaHeader::create($cartolaNew);
        $this->consultarCartolas();
        $this->cuentaSeleccionada($this->cuenta_selec);
    }

    public function pagosProgramados($id)
    {
        $customerMovimiento = CustomerMovimiento::find($id);
        $cabecerPlazo = HeaderCalculadora::where('customer_movimientos_id', $id)->first();
        $detalles = CustomerMovimientoDetalleCalculadora::where('customer_movimientos_id', $id)
            ->where('header_calculadora_id', $cabecerPlazo->id)
            ->get();
        $sumaDetallesPendientes = CustomerMovimientoDetalleCalculadora::where('customer_movimientos_id', $id)
            ->where('header_calculadora_id', $cabecerPlazo->id)
            ->where('status', 'PENDIENTE')
            ->sum('total');
        $detallesPagados = CustomerMovimientoDetalleCalculadora::where('customer_movimientos_id', $id)
            ->where('header_calculadora_id', $cabecerPlazo->id)
            ->where('status', 'PENDIENTE')
            ->count();
        $this->detallePlazoFijo = $detalles;
        $cuentaCliente = CustomerTipoAhorros::where('customer_id', $this->customer_selec)
            ->where('tipo_ahorros_programados_detalle_id', '0')
            ->get();
        $this->cuentaPagarPlazo = $cuentaCliente;
        $this->cuentaMaestraPlazoFijo = $customerMovimiento;
        $this->pagarMaestra = ($detallesPagados > 0) ? false : true;
        $this->seleccionPlazoPagar = true;
        $company = Company::find(Auth::user()->company_id);
        $totalCalculado = $customerMovimiento->valor_movimiento + $sumaDetallesPendientes;
        $totalCalculadoGanado = $sumaDetallesPendientes;
        $this->valorCalculadoPrestamoFijo = number_format($totalCalculado - (($company->penalidad_plazo_fijo / 100) * $totalCalculadoGanado), 2, '.', '');
        $this->valor_acreditar_retirar = 0;
        $this->valor_total_acreditar_retirar = $this->valorCalculadoPrestamoFijo;
    }
    public function entregarValoresEncaje()
    {
        $this->porcentajeRetencion = 0;
        $this->ValorDepositoEncaje = 0;
        $this->ValorRetenidoEncaje = 0;
        $this->ValorEntregarEncaje = 0;
        $entragarEncaje  = EntregaEncajes::where('customer_tipo_ahorro_id', $this->cuenta_selec)
            ->where('status', 'PENDIENTE')
            ->first();
        $cuentaCliente = CustomerTipoAhorros::where('customer_id', $this->customer_selec)
            ->where('tipo_ahorros_programados_detalle_id', '0')
            ->where('id', '!=', $this->cuenta_selec)
            ->get();
        $this->cuentaClienteAhorro = $cuentaCliente;

        if ($entragarEncaje) {

            $this->porcentajeRetencion = $entragarEncaje->porcentaje_retenido;
            $this->fechaRetiroEncaje = date('Y-m-d');
            $this->horaRetiroEncaje = date('H:i:s');
            $this->ValorDepositoEncaje = $entragarEncaje->valor;
            $this->ValorRetenidoEncaje = $entragarEncaje->valor_retenido;
            $this->ValorEntregarEncaje = $entragarEncaje->valor_entregado;
            $this->porcentaje_plazo_fijo = $entragarEncaje->porcentaje_plazo_fijo;
            $this->valor_plazo_fijo = $entragarEncaje->valor_plazo_fijo;
            $this->cuenta_valor = $entragarEncaje->valor_plazo_fijo;
            $this->tipo_ahorros_programados_detalle_id = $entragarEncaje->taza_plazo_fijo;
            $this->cuenta_pago = $entragarEncaje->pago_plazo_fijo;
            $this->diasManual = $entragarEncaje->dias_plazo_fijo;
            $this->beneficiarioProgramado = $entragarEncaje->beneficiario_plazo_fijo;
        } else {
            $color = 'danger';
            $mensaje = 'No se puede realizar la operacion no tiene una solicitud pendiente';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('closeModal');
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
    }

    public function entregarEncaje()
    {

        if ($this->ValorEntregarEncaje <= 0) {
            $color = 'danger';
            $mensaje = 'No tine valores de encaje para acreditar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        $this->validate(
            [
                'cuentasCliente' => 'required',
            ],
            [
                'cuentasCliente.required' => 'Necesita seleccionar una cuenta para entregar el encaje.',
            ]
        );
        $buscarCuentaEncaje = TipoAhorros::where('programado', true)
            ->first();
        if (!$buscarCuentaEncaje) {
            $this->dispatchBrowserEvent('closeModal');
            $color = 'danger';
            $mensaje = 'No tiene una cuenta de encaje configurada para hacer el ingreso del valor de penalidad de encaje';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        } else {
            $this->selectedTipoAhorro = $buscarCuentaEncaje->id;
        }
        $bovedas = Bovedas::where('status', 1)->where('boveda', 1)->first();
        if ($bovedas) {
            $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'IVRE')->first();
            $cuentaSeleccionada = CustomerTipoAhorros::find($this->cuenta_selec);
            $solicitud = new DescargoBovedasHeader();
            $solicitud->company_id = Auth::user()->company_id;
            $solicitud->cuenta_origen = $cuentaSeleccionada->id;
            $solicitud->operaciones_descargo_bovedas_id = $operacion->id;
            $solicitud->boveda_origen_id = $bovedas->id;
            $solicitud->boveda_destino_id = 0;
            $solicitud->valor = $this->ValorRetenidoEncaje;
            $solicitud->observacion = $operacion->descripcion . ' de la cuenta ' . $cuentaSeleccionada->codigo;
            $solicitud->fecha_creacion = date('Y-m-d');
            $solicitud->estado = 'FINALIZADO';
            $solicitud->save();
        } else {
            $this->dispatchBrowserEvent('closeModal');
            $color = 'danger';
            $mensaje = 'No tiene una bóveda configurada para hacer el ingreso del valor de penalidad de encaje';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        $this->movimientoOperacionesEncajeCliente();
        $this->registroEntregaEncaje();
        if ($this->porcentaje_plazo_fijo > 0) {

            $this->storeCuentaProgramadaEncaje();
        }
        $this->dispatchBrowserEvent('closeModal');
    }

    public function storeCuentaProgramadaEncaje()
    {
        $tipoAhorros = TipoAhorros::find($this->selectedTipoAhorro);
        $this->numeroCuenta = CuentasController::buscarNumeroCuenta();

        $nuevoCodigo = $this->numeroCuenta;
        $newCuenta = new CustomerTipoAhorros();
        $newCuenta->company_id = Auth::user()->company_id;
        $newCuenta->customer_id = $this->customer_selec;
        $newCuenta->tipo_ahorros_id = $this->selectedTipoAhorro;
        $newCuenta->codigo = $nuevoCodigo;
        $newCuenta->valor = $this->cuenta_valor;
        $newCuenta->tipo_ahorros_programados_detalle_id = $this->tipo_ahorros_programados_detalle_id;
        $newCuenta->pago = $this->cuenta_pago;
        $newCuenta->status = true;
        $newCuenta->save();
        //        inicio acreditacion automatica
        $this->valor_transaccion_calculadora = null;
        $this->plazoDiasProgramado = null;
        $this->tipoAhorrosPrgramadosDetalleId = null;
        if ($tipoAhorros->programado) {
            $this->tipo_transaccion = 'DFJ';
            $this->cuenta_selec = $newCuenta->id;
            $this->valor_transaccion = $this->cuenta_valor;
            $this->observacion_transaccion = 'ACREDITACIÓN DE VALORES POR PLAZO FIJO';
            $this->tipoAhorrosPrgramadosDetalleId = $this->tipo_ahorros_programados_detalle_id;
            $tipoAhorrosProgramados = TipoAhorrosProgramadosDetalle::find($newCuenta->tipo_ahorros_programados_detalle_id);
            if ($this->diasManual  > 0) {
                $this->plazoDiasProgramado = $this->diasManual;
            } else {
                $this->plazoDiasProgramado = $tipoAhorrosProgramados->rango_max;
            }
            $calculo = ($this->cuenta_valor) * ($tipoAhorrosProgramados->interes / 100);
            $penalizado = $calculo * (2 / 100);
            $diasAlCalculo = $tipoAhorrosProgramados->rango_max;
            $this->valor_transaccion_calculadora = number_format($this->cuenta_valor + $calculo - $penalizado);
            if ($this->diasManual  > 0) {
                $diasAlCalculo = $this->diasManual;
            }
            $fecha = \Carbon\Carbon::parse(date('Y-m-d'));
            $fechaMasDias = $fecha->addDays($diasAlCalculo);
            $fechaPago = $fechaMasDias->toDateString();

            $customer = Customer::find($this->customer_selec);
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'DFJ')
                ->first();
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,

                // Nuevos
                "banco_id" =>  null,
                "numero_deposito" =>  null,
                "forma_pago_id" => null,
                "forma_pago_name" => null,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "customer_tipo_ahorro_id" => $this->cuenta_selec,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $this->cuenta_valor,
                "saldo_general" => $valorTotal + $this->cuenta_valor,
                "observation" => null,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
                "valor_calculadora" => $this->valor_transaccion_calculadora,
                "plazo_dias_programado" => $this->plazoDiasProgramado,
                "tipo_ahorros_programados_detalle_id" => $this->tipoAhorrosPrgramadosDetalleId,
            ];
            $movimientos = CustomerMovimiento::create($data);
            $this->customerMovimientoCreateID = $movimientos->id;
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->cuenta_valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            BaseController::guardarValoresCartola($this->cuenta_selec, $customer->id, $movimientos, $transaction);





            $entero = intdiv($diasAlCalculo, 30);
            $calculoReal = (($calculo / 12) * $entero);
            $company = Company::find(Auth::user()->company_id);
            $penalizadoReal = 0;
            if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                //$penalizado = $this->valorGanadoVer * (2 / 100);
                $penalizadoReal = $calculoReal * (2 / 100);
            }
            $movimientosCustomer = CustomerMovimiento::find($this->customerMovimientoCreateID);
            $tipoAhorroProgramadoDetalle = TipoAhorrosProgramadosDetalle::find($movimientosCustomer->tipo_ahorros_programados_detalle_id);
            $dataHeader = [
                'company_id' => Auth::user()->company_id,
                'customer_id' => $movimientosCustomer->customer_id,
                'beneficiarioProgramado' => $this->beneficiarioProgramado,
                'customer_movimientos_id' => $movimientosCustomer->id,
                'tipo_pago' => $this->cuenta_pago,
                'tipo_ahorros_programados_detalle_id' => $movimientosCustomer->tipo_ahorros_programados_detalle_id,
                'interes' => $tipoAhorroProgramadoDetalle->interes,
                'dias_plazo' => $movimientosCustomer->plazo_dias_programado,
                'rentabilidad' => $calculoReal,
                'penalizado' => $penalizadoReal,
                'total' => $calculoReal - $penalizadoReal,
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $headerCalculadora = HeaderCalculadora::create($dataHeader);
            if ($this->cuenta_pago != 'C') {
                $data = array();
                $ganado = number_format($calculoReal / $entero, 2);
                $penali = 0;
                if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                    //$penali = number_format($penalizado / $entero, 2);
                    $penali = number_format($penalizadoReal / $entero, 2);
                }
                $fechaPago = date('Y-m-d');
                for ($i = 1; $i <= $entero; $i++) {
                    $fecha = \Carbon\Carbon::parse($fechaPago);
                    $fechaMasDias = $fecha->addMonths(1);
                    $fechaPago = $fechaMasDias->toDateString();
                    $data = [
                        'header_calculadora_id' => $headerCalculadora->id,
                        'customer_movimientos_id' => $this->customerMovimientoCreateID,
                        'rentabilidad' => $ganado,
                        'penalizado' => $penali,
                        'total' => ($ganado - $penali),
                        'fecha_pago' => $fechaPago,
                    ];
                    CustomerMovimientoDetalleCalculadora::create($data);
                }
                $this->mensualizados = $data;
            } else {
                $data = [
                    'header_calculadora_id' => $headerCalculadora->id,
                    'customer_movimientos_id' => $this->customerMovimientoCreateID,
                    'rentabilidad' => $calculoReal,
                    'penalizado' => $penalizadoReal,
                    'total' => ($calculoReal - $penalizadoReal),
                    'fecha_pago' => $fechaPago,
                ];
                CustomerMovimientoDetalleCalculadora::create($data);
            }
            $cunetaClinete = CustomerTipoAhorros::find($newCuenta->id);
            $cunetaClinete->dias_plazo = $this->plazoDiasProgramado;
            $cunetaClinete->save();
        }
        //        fin acreditacion automatica
        $this->dispatchBrowserEvent('closeModal');
    }


    public function movimientoOperacionesEncajeCliente()
    {
        //acreditacion a la cuenta seleccionada
        $customer = Customer::find($this->customer_selec);
        $cuentaAcreditar = CustomerTipoAhorros::find($this->cuentasCliente);
        $cuentaSeleccionada = CustomerTipoAhorros::find($this->cuenta_selec);
        $operacion = "IN";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
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
            "customer_tipo_ahorro_id" => $cuentaAcreditar->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $this->ValorEntregarEncaje,
            "saldo_general" => number_format($valorTotal, 5, '.', '') + $this->ValorEntregarEncaje,
            "observation" => 'Entrega de valores de la cuenta ' . $cuentaSeleccionada->codigo . ' por concepto de entrega de encaje',
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->ValorEntregarEncaje, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartola($cuentaAcreditar->id, $customer->id, $movimientos, $transaction);


        //retiro de la cuenta de encajes (valor de encaje)
        $operacion = "EG";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
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
            "customer_tipo_ahorro_id" => $cuentaSeleccionada->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $this->ValorEntregarEncaje,
            "saldo_general" => number_format($valorTotal, 5, '.', '') + $this->ValorEntregarEncaje,
            "observation" => 'Retiro del valor de encaje a la cuenta ' . $cuentaAcreditar->codigo,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->ValorEntregarEncaje, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartola($cuentaSeleccionada->id, $customer->id, $movimientos, $transaction);
        //retiro de la cuenta de encajes (valor Retenido Encaje)

        $operacion = "EG";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
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
            "customer_tipo_ahorro_id" => $cuentaSeleccionada->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $this->ValorRetenidoEncaje,
            "saldo_general" => number_format($valorTotal, 5, '.', '') + $this->ValorRetenidoEncaje,
            "observation" => 'Retiro del valor de encaje a la cuenta ' . $cuentaAcreditar->codigo,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->ValorRetenidoEncaje, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartola($cuentaSeleccionada->id, $customer->id, $movimientos, $transaction);

        //retiro del valor plazo fijo
        $operacion = "EG";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
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
            "customer_tipo_ahorro_id" => $cuentaSeleccionada->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $this->valor_plazo_fijo,
            "saldo_general" => number_format($valorTotal, 5, '.', '') + $this->valor_plazo_fijo,
            "observation" => 'Retiro del valor de encaje a la cuenta para creacion del plazo fijo ',
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->valor_plazo_fijo, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartola($cuentaSeleccionada->id, $customer->id, $movimientos, $transaction);
    }

    public function registroEntregaEncaje()
    {

        $entragarEncaje  = EntregaEncajes::where('customer_tipo_ahorro_id', $this->cuenta_selec)
            ->where('status', 'PENDIENTE')
            ->first();
        $entragarEncaje->user_entrega_id = Auth::user()->id;
        $entragarEncaje->user_entrega_name =  Auth::user()->username;
        $entragarEncaje->date_entrega = $this->fechaRetiroEncaje;
        $entragarEncaje->hour_entrega = $this->horaRetiroEncaje;
        $entragarEncaje->status = 'ENTREGADO';
        $entragarEncaje->save();


        $cuentaSeleccionada = CustomerTipoAhorros::find($this->cuenta_selec);
        $operacion = "IRE";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $customer = Customer::find($this->customer_selec);
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
            "valor_movimiento" => $this->ValorRetenidoEncaje,
            "saldo_general" => $valorTotal + $this->ValorRetenidoEncaje,
            "observation" => 'Ingreso de valores de encaje de la cuenta ' . $cuentaSeleccionada->codigo,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->ValorRetenidoEncaje, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
    }
    public function historialEncaje()
    {
        $this->encajesEntregados = EntregaEncajes::where('customer_tipo_ahorro_id', $this->cuenta_selec)->where('status', 'ENTREGADO')->get();
    }

    public function imprimirEntregaEncaje($id)
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['encaje'] = EntregaEncajes::whereIn('customer_id', Customer::where('company_id', Auth::user()->company_id)->select('id'))->findOrFail($id);
        $data['customer'] = Customer::where('company_id', Auth::user()->company_id)->findOrFail($data['encaje']->customer_id);

        // Fecha original
        $fechaOriginal = $data['encaje']->date_created;

        // Crear una instancia de Carbon con la fecha original
        $fecha = Carbon::createFromFormat('Y-m-d', $fechaOriginal);
        // Mantener la fecha real del comprobante.
        $data['fechaFormateada'] = $fecha->locale('es')->translatedFormat('d \d\e F \d\e\l Y');

        $pdf = PDF::loadView('reportes.generarPdfEncajeRetenido', $data);

        // Devuelve el PDF como descarga
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Encaje Retenido.pdf');
    }

    public function storeCuentaNovacion()
    {

        if ($this->valor_acreditar_retirar < 0) {
            $this->validate(
                [
                    'cuentaPagarPlazoListaNovar' => 'required',

                ],
                [
                    'cuentaPagarPlazoListaNovar.required' => 'Necesita seleccionar una cuenta si va a retirar dinero',

                ]
            );
        }
        $customerMovimiento = $this->cuentaMaestraPlazoFijo;
        $customerTipoAhorros = CustomerTipoAhorros::find($customerMovimiento->customer_tipo_ahorro_id);
        $this->selectedTipoAhorro = $customerTipoAhorros->tipo_ahorros_id;
        $tipoAhorros = TipoAhorros::find($this->selectedTipoAhorro);
        if ($tipoAhorros->programado) {
            $cajaAbierta = Cajas::where('status', 'ABIERTA')
                ->where('date_inicial', date('Y-m-d'))
                ->where('user_inicial_id', Auth::user()->id)
                ->count();
            if ($cajaAbierta == 1) {
                $this->validarCaja = true;
            } else {
                $this->validarCaja = false;
            }
            if ($this->validarCaja == false) {
                $color = 'danger';
                $mensaje = 'No se puede realizar esta operacion hasta que tenga una caja abierta';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;
            }

            $this->validate(
                [
                    'valor_total_acreditar_retirar' => 'required',
                    'tipo_ahorros_programados_detalle_id_novacion' => 'required',
                    'cuenta_pago_novacion' => 'required',
                    'diasManualNovacion' => ($this->usarManual == true) ? 'required' : '',
                ],
                [
                    'valor_total_acreditar_retirar.required' => 'Necesita ingresar un valor.',
                    'tipo_ahorros_programados_detalle_id_novacion.required' => 'Necesita seleccionar un porcentaje.',
                    'cuenta_pago_novacion.required' => 'Necesita seleccionar la forma de desembolso.',
                    'diasManualNovacion.required' => 'Usted selecciono ingresar el plazo manual este campo es obligatorio.',
                ]
            );
        }
        $valorParaNUevoPlazo = $this->valor_total_acreditar_retirar;
        $this->cuenta_valor = $this->valor_total_acreditar_retirar;
        $this->tipo_ahorros_programados_detalle_id = $this->tipo_ahorros_programados_detalle_id_novacion;

        $this->cuenta_pago = $this->cuenta_pago_novacion;
        $this->diasManual = $this->diasManualNovacion;


        $ultimoCodigo = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)
            ->max('codigo');
        $nuevoCodigo = str_pad((int) $ultimoCodigo + 1, 10, '0', STR_PAD_LEFT);
        $newCuenta = new CustomerTipoAhorros();
        $newCuenta->company_id = Auth::user()->company_id;
        $newCuenta->customer_id = $this->customer_selec;
        $newCuenta->tipo_ahorros_id = $this->selectedTipoAhorro;
        $newCuenta->codigo = $nuevoCodigo;
        $newCuenta->valor = $this->cuenta_valor;
        $newCuenta->tipo_ahorros_programados_detalle_id = $this->tipo_ahorros_programados_detalle_id;
        $newCuenta->pago = $this->cuenta_pago;
        $newCuenta->status = true;
        $newCuenta->save();
        //        inicio acreditacion automatica
        $this->valor_transaccion_calculadora = null;
        $this->plazoDiasProgramado = null;
        $this->tipoAhorrosPrgramadosDetalleId = null;
        if ($tipoAhorros->programado) {
            $this->tipo_transaccion = 'IN';
            $this->cuenta_selec = $newCuenta->id;
            $this->valor_transaccion = $this->cuenta_valor;
            $this->observacion_transaccion = 'ACREDITACIÓN DE VALORES POR PLAZO FIJO';
            $this->tipoAhorrosPrgramadosDetalleId = $this->tipo_ahorros_programados_detalle_id;
            $tipoAhorrosProgramados = TipoAhorrosProgramadosDetalle::find($newCuenta->tipo_ahorros_programados_detalle_id);

            if ($this->diasManual) {
                $this->plazoDiasProgramado = $this->diasManual;
            } else {
                $this->plazoDiasProgramado = $tipoAhorrosProgramados->rango_max;
            }
            $calculo = ($this->cuenta_valor) * ($tipoAhorrosProgramados->interes / 100);
            $penalizado = $calculo * (2 / 100);
            $diasAlCalculo = $tipoAhorrosProgramados->rango_max;
            $this->valor_transaccion_calculadora = number_format($this->cuenta_valor + $calculo - $penalizado);
            if ($this->diasManual) {
                $diasAlCalculo = $this->diasManual;
            }
            $fecha = \Carbon\Carbon::parse(date('Y-m-d'));
            $fechaMasDias = $fecha->addDays($diasAlCalculo);
            $fechaPago = $fechaMasDias->toDateString();
            $vieneAhorroProgramadoONovacion = true;
            $this->storeAhorroRetiro($vieneAhorroProgramadoONovacion);
            $entero = intdiv($diasAlCalculo, 30);
            $calculoReal = (($calculo / 12) * $entero);
            $company = Company::find(Auth::user()->company_id);
            $penalizadoReal = 0;
            if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                $penalizadoReal = $calculoReal * (2 / 100);
            }



            $transactionUpdate = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'IFN')
                ->first();

            $movimientosCustomer = CustomerMovimiento::find($this->customerMovimientoCreateID);
            $movimientosCustomer->type_transaction_id = $transactionUpdate->id;
            $movimientosCustomer->type_transaction_action = $transactionUpdate->action;
            $movimientosCustomer->save();

            $movimientoHistorial = CustomerHistorial::where('customer_movimiento_code', $movimientosCustomer->code)->first();
            $movimientoHistorial->type_transaction_id = $transactionUpdate->id;
            $movimientoHistorial->type_transaction_action = $transactionUpdate->action;
            $movimientoHistorial->save();


            $tipoAhorroProgramadoDetalle = TipoAhorrosProgramadosDetalle::find($movimientosCustomer->tipo_ahorros_programados_detalle_id);
            $dataHeader = [
                'company_id' => Auth::user()->company_id,
                'customer_id' => $movimientosCustomer->customer_id,
                'beneficiarioProgramado' => $this->beneficiarioProgramadoNovar,
                'customer_movimientos_id' => $movimientosCustomer->id,
                'tipo_pago' => $this->cuenta_pago,
                'tipo_ahorros_programados_detalle_id' => $movimientosCustomer->tipo_ahorros_programados_detalle_id,
                'interes' => $tipoAhorroProgramadoDetalle->interes,
                'dias_plazo' => $movimientosCustomer->plazo_dias_programado,
                'rentabilidad' => $calculoReal,
                'penalizado' => $penalizadoReal,
                'total' => $calculoReal - $penalizadoReal,
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $headerCalculadora = HeaderCalculadora::create($dataHeader);
            if ($this->cuenta_pago != 'C') {
                $data = array();
                $ganado = number_format($calculoReal / $entero, 2);

                $penali = 0;
                if ($company->comercial_name != "CAJA SAN JOSE ALTO") {
                    $penali = number_format($penalizadoReal / $entero, 2);
                }

                $fechaPago = date('Y-m-d');
                for ($i = 1; $i <= $entero; $i++) {
                    $fecha = \Carbon\Carbon::parse($fechaPago);
                    $fechaMasDias = $fecha->addMonths(1);
                    $fechaPago = $fechaMasDias->toDateString();
                    $data = [
                        'header_calculadora_id' => $headerCalculadora->id,
                        'customer_movimientos_id' => $this->customerMovimientoCreateID,
                        'rentabilidad' => $ganado,
                        'penalizado' => $penali,
                        'total' => ($ganado - $penali),
                        'fecha_pago' => $fechaPago,
                    ];
                    CustomerMovimientoDetalleCalculadora::create($data);
                }
                $this->mensualizados = $data;
            } else {
                $data = [
                    'header_calculadora_id' => $headerCalculadora->id,
                    'customer_movimientos_id' => $this->customerMovimientoCreateID,
                    'rentabilidad' => $calculoReal,
                    'penalizado' => $penalizadoReal,
                    'total' => ($calculoReal - $penalizadoReal),
                    'fecha_pago' => $fechaPago,
                ];
                CustomerMovimientoDetalleCalculadora::create($data);
            }
            $cunetaClinete = CustomerTipoAhorros::find($newCuenta->id);
            $cunetaClinete->dias_plazo = $this->plazoDiasProgramado;
            $cunetaClinete->save();
        }
        $this->generarMovimientonovar($this->valor_acreditar_retirar, $this->cuenta_valor, $valorParaNUevoPlazo);
        $this->cerrarCuentasFijas();
        $this->dispatchBrowserEvent('closeModal');
    }
    public function generarMovimientonovar($valor, $valorNetear, $valorParaNUevoPlazo)
    {
        $valorMovimiento = abs($valor);
        if ($valorMovimiento != 0) {
            if ($valor > 0) {
                $this->tipo_transaccion = 'IFN';
            } else {
                $this->tipo_transaccion = 'RFN';

                $customer = Customer::find($this->customer_selec);
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'PVP')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $observacion = '(PAGO GLOBAL)SE PAGA EL VALOR $ ' . $valorMovimiento . ' POR PLAZO FIJO';
                $data = [
                    "code" => $code,
                    "company_id" => Auth::user()->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "afecta" => $transaction->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "customer_tipo_ahorro_id" => $this->cuentaPagarPlazoListaNovar,
                    "type_transaction_id" => $transaction->id,
                    "type_transaction_name" => $transaction->name,
                    "type_transaction_action" => $transaction->action,
                    "valor_movimiento" => $valorMovimiento,
                    "saldo_general" => $valorTotal + $valor,
                    "observation" => $observacion,
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),
                ];
                $movimientos = CustomerMovimiento::create($data);
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorMovimiento, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
                BaseController::guardarValoresCartola($this->cuentaPagarPlazoListaNovar, $customer->id, $movimientos, $transaction);
                BaseController::enviarMail($movimientos);
            }
            $customer = Customer::find($this->customer_selec);
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', $this->tipo_transaccion)
                ->first();
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();
            $data = [
                "code" => $code,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $valorMovimiento,
                "saldo_general" => $valorTotal + $valorMovimiento,
                "observation" => $transaction->description,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),

            ];
            $movimientos = CustomerMovimiento::create($data);
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorMovimiento, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        }
        $customer = Customer::find($this->customer_selec);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'DNN')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();

        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "customer_id" => $customer->id,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $valorNetear,
            "saldo_general" => $valorTotal + $valorNetear,
            "observation" => $transaction->description,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),

        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorNetear, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
    }

    public function cerrarCuentasFijas()
    {
        $customerHeader = $this->cuentaMaestraPlazoFijo;

        $detallesPagadosAll = CustomerMovimientoDetalleCalculadora::where('customer_movimientos_id', $customerHeader->id)
            ->get();
        foreach ($detallesPagadosAll as $val) {
            $detallesPagados = CustomerMovimientoDetalleCalculadora::find($val->id);
            $valor = $detallesPagados->total;
            $detallesPagados->status = 'PAGADO';
            $detallesPagados->save();
        }

        $customerMovimiento = CustomerMovimiento::find($customerHeader->id);
        $valor = $customerMovimiento->valor_movimiento;
        $customerMovimiento->status_programado = 'PAGADO';
        $customerMovimiento->save();

        $ahorro = CustomerTipoAhorros::find($customerHeader->customer_tipo_ahorro_id);
        $ahorro->status = 0;
        $ahorro->save();
    }
    public function novarPlazoFijo()
    {
        $this->dispatchBrowserEvent('closeModal', ['modalId' => 'modalGeneral8']);
    }
    public function actualizarValorNovacion()
    {
        $valorTotalAcreditarRetirar = $this->valorCalculadoPrestamoFijo + $this->valor_acreditar_retirar;
        $this->valor_total_acreditar_retirar = $valorTotalAcreditarRetirar;
    }

    public function desembolsarValorPlazo($id, $tipo)
    {
        $this->validate(
            [
                'cuentaPagarPlazoLista' => 'required',

            ],
            [
                'cuentaPagarPlazoLista.required' => 'Necesita seleccionar una cuenta a cual desembolsar.',

            ]
        );
        $valor = 0;
        $observacion = '';
        $buscar = 0;
        if ($tipo == 1) {
            $detallesPagados = CustomerMovimientoDetalleCalculadora::find($id);
            $valor = $detallesPagados->total;
            $detallesPagados->status = 'PAGADO';
            $detallesPagados->save();
            $observacion = 'SE PAGA EL VALOR $ ' . $valor . ' POR PLAZO FIJO CORRESPONDIENTE A LA FECHA ' . $detallesPagados->fecha_pago;
            $buscar = $detallesPagados->customer_movimientos_id;
        } else {
            $customerMovimiento = CustomerMovimiento::find($id);
            $valor = $customerMovimiento->valor_movimiento;
            $customerMovimiento->status_programado = 'PAGADO';
            $customerMovimiento->save();
            $observacion = '(PAGO GLOBAL)SE PAGA EL VALOR $ ' . $valor . ' POR PLAZO FIJO';
            $buscar = $id;
            $ahorro = CustomerTipoAhorros::find($this->cuenta_selec);
            $ahorro->status = 0;
            $ahorro->save();
        }

        $customer = Customer::find($this->customer_selec);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PVP')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "customer_id" => $customer->id,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "customer_tipo_ahorro_id" => $this->cuentaPagarPlazoLista,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $valor,
            "saldo_general" => $valorTotal + $valor,
            "observation" => $observacion,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartola($this->cuentaPagarPlazoLista, $customer->id, $movimientos, $transaction);
        BaseController::enviarMail($movimientos);
        $this->pagosProgramados($buscar);
    }

    public function verpagosPlazoFijo($id)
    {
        //$this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('closeModal', ['modalId' => 'modalGeneral7']);

        $movimiento = CustomerMovimiento::where('customer_tipo_ahorro_id', $id)->first();

        $this->pagosProgramados($movimiento->id);
    }
    public function historialAhorros()
    {
        $cliente = Customer::find($this->customer_selec);
        $cuentas = CustomerTipoAhorros::join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
            ->where('customer_id', $this->customer_selec)
            ->select(
                'customer_tipo_ahorros.id',
                'customer_tipo_ahorros.created_at',

                'customer_id',
                'tipo_ahorros_id',
                'codigo',
                'valor',
                'tipo_ahorros_programados_detalle_id',
                'pago',
                'dias_plazo',
                'cumplimiento',
                'programado',
                'name',
            )
            ->get();
        $this->historialCuenrtas = $cuentas;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount($customer_id)
    {
        if ($customer_id > 0) {
            $this->customer_selec = $customer_id;
            $customerFind = Customer::where('company_id', Auth::user()->company_id)->findOrFail($customer_id);
            $this->search = $customerFind->numero_documento;
        }
        $tipoAhorros = TipoAhorros::where('status', true)->get();
        $this->tipoAhorros = $tipoAhorros;

        // Formas de Pago
        $formasPago = FormasPago::where('status', true)->get();
        $this->formasPago = $formasPago;

        $tipoAhorro = TipoAhorros::where('company_id', Auth::user()->company_id)->where('programado', true)->first();
        if ($tipoAhorro) {
            $this->interes = TipoAhorrosProgramadosDetalle::where('tipo_ahorros_id', $tipoAhorro->id)->get();
        }
    }

    public function render()
    {
        //dd('entra');
        $cliente = Customer::find($this->customer_selec);
        $cuentas = CustomerTipoAhorros::where('customer_id', $this->customer_selec)
            ->where('status', 1)
            ->get();
        foreach ($cuentas as $key => $cuent) {
            /*
            $ingresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                //                ->where('customer_historials.customer_code', $cliente->code)
                ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC'])
                // ->where('type_transaction_action', 'S')
                // ->where('type_transaction_name', 'INGRESOS')
                ->where('customer_historials.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_historials.status', true)
                ->sum('customer_historials.valor_movimiento');
                */

            $ingresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'TRR', 'APS'])
                ->where('customer_movimientos.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_movimientos.status', true)
                ->sum('customer_movimientos.valor_movimiento');

            /*
            $egresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                //                ->where('customer_historials.customer_code', $cliente->code)
                ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND'])
                // ->where('type_transaction_action', 'R')
                // ->where('type_transaction_name', 'EGRESOS')
                ->where('customer_historials.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_historials.status', true)
                ->sum('customer_historials.valor_movimiento');
            */
            $egresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND', 'DCT', 'TRE'])
                ->where('customer_movimientos.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_movimientos.status', true)
                ->sum('customer_movimientos.valor_movimiento');

            $valor = $ingresos - $egresos;
            $cuent->saldo = number_format(round($valor, 2), 2, '.', '');
            $cuent->porcentaje = '';
            if ($cuent->tipo_ahorros_programados_detalle_id) {
                $tipoAhorroDetalle = TipoAhorrosProgramadosDetalle::find($cuent->tipo_ahorros_programados_detalle_id);
                if ($tipoAhorroDetalle != null) {
                    $cuent->porcentaje = $tipoAhorroDetalle->interes;
                }
            }
        }
        $hoy = now()->format('Y-m-d');
        $customer = Customer::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
            })
            ->select(
                '*',
                DB::raw('(SELECT COUNT(*) FROM customer_tipo_ahorros WHERE customer_tipo_ahorros.customer_id = customer.id) AS total_tipos_ahorro'),
                DB::raw("DATE(customer.created_at) = '{$hoy}' as nuevo")
            )
            ->where('status', true)
            ->orderBy('created_at', 'DESC')
            ->paginate(13);
        if (isset($cliente->code)) {
            if ($this->cuenta_selec > 0) {
                $detalle = CustomerMovimiento::select('customer_movimientos.*', 'type_transactions.name_corto')
                    ->join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                    ->where('customer_movimientos.customer_code', $cliente->code)
                    ->where('customer_movimientos.company_id', Auth::user()->company_id)
                    ->whereIn('type_transactions.name_corto', ['IN', 'EG', 'SE', 'SC', 'DEA', 'SOL', 'CVN', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'DND', 'DCT', 'TRR', 'TRE', 'APS'])
                    ->where('customer_movimientos.customer_tipo_ahorro_id', $this->cuenta_selec)
                    ->where('customer_movimientos.status', true)
                    //->orderBy('customer_movimientos.id', 'asc')
                    //->select('*', DB::raw('(SELECT COUNT(*) FROM wha_envios WHERE wha_envios.proviene_id = customer_movimientos.id AND wha_envios.type_transacction_id = customer_movimientos.type_transaction_id) AS total'))
                    ->get();
                $valoTotal = 0;
                foreach ($detalle as $valdet) {
                    if ($valdet->name_corto != 'SE') {
                        if ($valdet->type_transaction_action == 'S') {
                            $valoTotal += $valdet->valor_movimiento;
                        } else {
                            $valoTotal -= $valdet->valor_movimiento;
                        }
                    }
                    $valdet->saldoValor = $valoTotal;
                }
            } else {
                $detalle = new CustomerHistorial();
            }
        } else {
            $detalle = new CustomerHistorial();
        }
        $filesCuenta = CustomerFile::where('customer_id', $this->customer_selec)->where('customer_tipo_ahorros_id', $this->cuenta_selec)->get();
        $company = Company::find(Auth::user()->company_id);
        $clientesTodos = Customer::all();

        // FIltrar bancos
        if ($this->tipo_transaccion == 'IN') {
            $bancos = Bancos::where('tipo_cuenta_id', '>', 0)->where('numero_cuenta', '!=', '')->where('status', true)->get();
        } else {
            $bancos = Bancos::where('tipo_cuenta_id', '=', 0)->where('status', true)->get();
        }

        return view('livewire.cuentas.cuentas-component', compact('customer', 'cliente', 'cuentas', 'detalle', 'filesCuenta', 'company', 'clientesTodos', 'bancos'));
    }
}
