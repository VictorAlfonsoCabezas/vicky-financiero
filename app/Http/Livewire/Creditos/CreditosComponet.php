<?php

namespace App\Http\Livewire\Creditos;

use App\Models\Customer;
use App\Models\Company;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use App\Models\AsientosHeader;
use App\Models\AsientosDetalle;
use App\Models\ConfigPlanHeader;
use App\Models\ConfigPlanDetalle;
use App\Models\PlanCuentas;
use App\Models\Bancos;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerParentezco;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimiento;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\Bovedas;
use App\Models\CustomerFile;
use App\Models\FormasPago;
use App\Models\CustomerTipoAhorros;
use App\Models\WhaEnvios;
use App\Models\Cajas;
use App\Models\Country;
use App\Models\CustomerHistorial;
use App\Models\DescargoBovedasHeader;
use App\Models\OperacionesDescargoBovedas;
use App\Models\RegistroFormasPago;
use App\Models\Garantes;
use App\Models\RegistroFormasLiquidacion;

use App\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use PDF;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Luecano\NumeroALetras\NumeroALetras;

class CreditosComponet extends Component
{

    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['pagarnovacion', 'reversarCredito'];


    public $id_seleccionado = 0;
    public $id_credito = 0;
    public $search = '';
    public $customer_selec = 0;
    public $listaCreditos = [];
    public $customerID = '';
    public $nombres = '';
    public $apellidos = '';
    public $telefono = '';
    public $totalCreditos = '';
    public $valor_simulador = '';
    public $cuotas_simulador = '';
    public $tipo_simulador = '';
    public $prestamo_simulador = '';
    public $generar = '';
    public $fecha_prestamo = '';
    public $opcionesPrestamo = [];
    public $listaLetras = [];
    public $limpiar = '';
    public $valorCuota = 0;
    public $valorCreditoSumado = 0;
    public $valorCreditoSumadoPrimeraLetra = 0;
    public $valorDesgravament = 0;
    public $verTabla = '';
    public $textoInicio = '';
    public $verPdf = false;
    public $verBtnGenerar = true;
    public $mensajeCreacion = '1';
    public $codigoPrestamo = '';
    public $companyVa = '';
    public $familiares = [];
    public $garante = '';
    public $obligarGarante = '';
    public $resumenLetra = '';
    public $headerPago = 0;
    public $detallePago = [];
    public $numeroDeLetra = '';
    public $formaPago = '';
    public $fechaPagoLetra = '';
    public $interesMora = '';
    public $valorPagarLetra = '';
    public $observacionPagoLetra = '';
    public $idDeLetra = '';
    public $interesSuma = '';
    public $companyPagare = false;
    public $companyLetraCambio = false;
    public $diarioLetras = false;
    public $mostrarSimulador = false;
    public $mostrarListaCreditos = false;
    public $styleMostrarSimulador = '';
    public $StyleMostrarListaCreditos = '';
    public $StyleMostrarCalificacion = '';
    public $mostrarCalificacion = false;
    public $procesando = false;
    public $archivo;
    public $descripcion = '';
    public $mensaje = '';
    public $creditoEditar = '';
    public $codigoEdit = '';
    public $valorEditar = '';
    public $cuotasEditar = '';
    public $fechaEditar = '';
    public $tipoCreditoEditar = '';
    public $prestamoEditar = '';
    public $porcentajeEditar = '';
    public $valorCuotaEdit = '';
    public $valorDesgravamentEdi = '';
    public $interesSumaEdit = '';
    public $listaLetrasEdit = '';
    public $headerPagoCode = '';
    public $descLetra = '';
    public $opcionesPrestamoEditar = [];
    public $verBotonlistaPrestamos = false;
    public $creditoIdnovacion = '';
    public $creditoIdnovacionDatos = '';
    public $valorVencido = 0;
    public $valorCumplido = 0;
    public $valorCapitalAmortizado = 0;
    public $cuotasNovaNuew = '';
    public $tipoNovaCredito = '';
    public $prestamoNova = '';
    public $valorprestamoNova = '';
    public $estadoCreditoNovacion = false;
    public $idActualizarAnteriorNovacion = '';
    public $capitalLiquidar = '';
    public $desgravamenLiquidar = '';
    public $interesLiquidar = '';
    public $totalLiquidar = '';
    public $feacha_actualLiquidar = '';
    public $detalleLiquidar = [];
    public $carpetaLiquidar = '';
    public $formasPagarCredito = [];
    public $garantesArreglo = [];
    public $valor_forma_pago = '';
    public $formaPagoLiquidar = '';
    public $formasPagarCreditoLiquidar = [];
    public $valor_forma_pago_liquidar = '';
    public $cuenta_credito = '';
    public $cuentasDes = [];
    public $creditoDineroEntregar = '';
    public $gasto_cobranza = 0;
    public $ingresos_netos = 0;
    public $gastos_mensuales = 0;
    public $tasa_interes = "";
    public $plazo_prestamo = "";
    public $activos = 0;
    public $pasivos = 0;
    public $datosCalculados = [];

    public $pago_maximo_mensual_de_deuda = 0;
    public $capacidad_de_endeudamiento = 0;
    public $patrimonio = 0;
    public $formaPagoEntregaCredito = '';
    public $documento_desembolso = '';
    public $obligarCuentaInterna = 0;
    public $valorEncajado = 0;
    public $pararTodo = 0;
    public $valor_ahorro = 0;
    public $valor_ahorrar_credito = 0;
    public $verCuentaAhorroPrestamo = false;
    public $codeString = '';
    public $cabeceraCreada = '';
    public $banco_id = '';
    public $banco_pago_id = '';

    public $numero_comprobante = '';
    public $mostrarComprobante = false;


    public function imprimirTicket($id)
    {

        $data['letra'] = CreditFolderDetail::find($id);
        $data['letraSiguiente'] = CreditFolderDetail::find($id + 1);
        $data['letraSiguienteFecha'] = '';
        if ($data['letraSiguiente']) {
            $data['letraSiguienteFecha'] = $data['letraSiguiente']->date_vencimiento;
        }

        $data['letrasPendientes'] = CreditFolderDetail::where('code_folder_header', $data['letra']->code_folder_header)
            ->where('status', 'PENDIENTE')->sum('valor_cuota');
        $data['letrasPagadas'] = CreditFolderDetail::where('code_folder_header', $data['letra']->code_folder_header)
            ->where('status', 'PAGADA')->sum('valor_cuota');

        $data['cabecera'] = CreditFolderHeader::where('code', $data['letra']->code_folder_header)->first();
        $data['company'] = Company::find(Auth::user()->company_id);
        $data['movimiento'] = CustomerMovimiento::where('credit_folder_details_id', $id)->first();
        $data['movimientosCustomer'] = CustomerMovimiento::where('credit_folder_details_id', $id);
        $data['formasPago'] = '';
        $formasPagoName = '';
        foreach ($data['movimientosCustomer']->get() as $value) {
            $formaPago = FormasPago::find($value->forma_pago_id);
            if ($formaPago) {
                $formasPagoName .= $formaPago->nombre . ', ';
            }
        }
        $data['formasPago'] = $formasPagoName;
        $data['usuario'] = "";
        if ($data['movimiento']) {
            $usuarioCreacion = User::find($data['movimiento']->user_created_id);
            if ($usuarioCreacion) {
                $data['usuario'] = $usuarioCreacion->username;
            }
        }

        $data['prestamo'] = Prestamos::find($data['cabecera']->tipo_prestamo);

        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['letra']->valor_pagado, 2, 'DÓLARES', 'CENTAVOS');

        // Crear PDF con tamaño de ticket (95mm de ancho)
        $pdf = PDF::loadView('ticket.letras', $data)->setPaper([0, 0, 269.29, 500], 'portrait');


        // Convertir en base64 para enviarlo a la vista
        $pdfBase64 = base64_encode($pdf->output());

        // Emitir evento a JavaScript
        $this->dispatchBrowserEvent('mostrar-pdf', ['pdf' => $pdfBase64]);
    }

    public function generarPdfLetrasCreditos()
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
        $data['codigoCredito'] = '';
        $data['generar'] = false;

        if ($this->generar == 1) {
            $data['generar'] = true;
            $data['codigoCredito'] = $this->codeString;
        }

        $data['customer'] = Customer::find($this->customerID);
        $cabecera = CreditFolderHeader::find($this->cabeceraCreada);

        if ($cabecera == null) {

            $data['garanteDato'] = Customer::find(0);
        } else {

            $data['garanteDato'] = Customer::find($cabecera->customer_garante_id);
        }

        $data['prestamo'] = Prestamos::find($this->prestamo_simulador);
        $data['valorAdministrativo'] = 0;
        $data['solicitadoPrestamo'] = $this->valor_simulador;
        if ($data['prestamo']->administrativo_porcentaje_valor == "VALOR") {
            $data['valorAdministrativo'] = $data['prestamo']->gasto_administrativo;
        } else {
            $solicitadoPrestamo = $this->valor_simulador;
            $data['valorAdministrativo'] = $solicitadoPrestamo * ($data['prestamo']->gasto_administrativo / 100);
        }

        $data['anios_pagar'] = $this->cuotas_simulador / 12;
        $data['cuotas_pagar'] = $this->cuotas_simulador;
        $data['valor_cuota'] = $this->valorCuota;
        $valoresGastosCalculo = BaseController::valoresGastosCalculo($data['prestamo'], $this->valor_simulador);
        $data['valorCreditoSumado'] = $valoresGastosCalculo['gasto1'] + $valoresGastosCalculo['gasto2'] + $valoresGastosCalculo['gasto3'];
        $data['interes_periodo'] = 0;
        $data['listaLetras'] = $this->listaLetras;
        $data['valor_ahorrar_credito'] = $this->valor_ahorrar_credito;
        $data['fecha_prestamo'] = $this->fecha_prestamo;

        foreach ($this->listaLetras as $letras) {
            $data['interes_periodo'] += $letras['interes'];
        }
        $data['garantesTabla'] = $this->garantesArreglo;
        $pdf = PDF::loadView('reportes.generarPdfLetrasCreditos', $data);

        // Devuelve el PDF como descarga
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'CONTRATO DE PRESTAMO.pdf');
    }
    public function seleccionarCliente($id)
    {
        if ($id > 0) {
            Customer::where('company_id', Auth::user()->company_id)->findOrFail($id);
        }
        if ($this->id_seleccionado != $id) {
            $this->headerPago = 0;
            $this->id_credito = 0;
            $this->resetErrorBag();
        }
        if (!$this->mostrarSimulador && !$this->mostrarListaCreditos) {
            $this->mostrarListaCreditos = true;
            $this->StyleMostrarListaCreditos = 'btn-primary';
        }
        $this->listaLetras = [];
        $this->interesSuma = '';
        if ($id > 0) {
            $this->customer_selec = $id;
            $company = Company::find(Auth::user()->company_id);
            $customer = Customer::where('company_id', Auth::user()->company_id)->findOrFail($id);
            $creditos = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('customer_id', $id)
                ->get();
            foreach ($creditos as $value) {
                $value->valorDeve = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
                $value->totalLetrasImpagas = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->count();
                if ($value->tipo_prestamo != null) {
                    $prestamos = Prestamos::find($value->tipo_prestamo);
                    $value->nombrePrestamo = $prestamos->name;
                } else {
                    $value->nombrePrestamo = '';
                }
            }
            $this->id_seleccionado = $id;
            $this->listaCreditos = $creditos;
            $this->nombres = $customer->nombres;
            $this->customerID = $customer->id;
            $this->apellidos = $customer->apellidos;
            $this->telefono = $customer->telefono;
            $this->totalCreditos = $creditos->count();
            $this->valor_simulador = '';
            $this->cuotas_simulador = '';
            $this->tipo_simulador = '';
            $this->prestamo_simulador = '';
            $this->generar = '';
            $this->fecha_prestamo = date('Y-m-d');
            $this->fechaPagoLetra = date('Y-m-d');
            $this->verBtnGenerar = true;
            $this->mensajeCreacion = '';
            $this->verPdf = false;
            $this->companyLetraCambio = $company->letra_cambio;
            $this->companyPagare = $company->pagare;
            $this->obligarGarante = $company->obligar_garante;
        } else {
            $this->id_seleccionado = 0;
        }
    }

    public function updatedFormaPago($value)
    {
        $this->verificarFormaPago();
        $this->generarListadeCreditos();
    }

    public function updatedFormaPagoLiquidar($value)
    {
        $forma = FormasPago::find($value);

        if ($forma && strtoupper($forma->nombre) == 'TRANSFERENCIA') {
            $this->mostrarComprobante = true;
        } else {
            $this->mostrarComprobante = false;

            $this->banco_pago_id = null;
            $this->numero_comprobante = null;
        }
    }

    public function verificarFormaPago()
    {
        $forma = \App\Models\FormasPago::find($this->formaPago);

        if ($forma && strtoupper($forma->nombre) == 'TRANSFERENCIA') {
            $this->mostrarComprobante = true;
        } else {
            $this->mostrarComprobante = false;
            $this->numero_comprobante = '';
        }
    }

    public function generarListadeCreditos()
    {
        $creditos = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $this->id_seleccionado)
            ->get();
        foreach ($creditos as $value) {
            $value->valorDeve = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            $value->totalLetrasImpagas = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->count();
            $value->pagare = false;
            $value->contrato = false;
            $value->letraCambio = false;
            $value->liquidacion_pendiente = RegistroFormasLiquidacion::where(
                'liquidacion_id',
                $value->id
            )
                ->where('solicitado', 3)
                ->exists();

            if ($value->tipo_prestamo != null) {
                $prestamos = Prestamos::find($value->tipo_prestamo);
                $value->nombrePrestamo = $prestamos->name;
                $value->pagare = $prestamos->pagare;
                $value->contrato = $prestamos->contrato;
                $value->letraCambio = $prestamos->letra_cambio;
            } else {
                $value->nombrePrestamo = '';
            }
        }
        $this->listaCreditos = $creditos;
    }

    public function formaLiquidacionCredito()
    {
        $formaPago = FormasPago::find($this->formaPagoEntregaCredito);
        $this->obligarCuentaInterna = $formaPago->credito_descargo_boveda;
    }

    public function generarDatos($id)
    {
        if ($id > 0) {
            $company = Company::find(Auth::user()->company_id);
            $customer = Customer::find($id);
            $creditos = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('customer_id', $id)
                ->get();
            foreach ($creditos as $value) {
                $value->valorDeve = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
                $value->totalLetrasImpagas = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->count();
                if ($value->tipo_prestamo != null) {
                    $prestamos = Prestamos::find($value->tipo_prestamo);
                    $value->nombrePrestamo = $prestamos->name;
                } else {
                    $value->nombrePrestamo = '';
                }
            }
            $this->id_seleccionado = $id;
            $this->listaCreditos = $creditos;
            $this->nombres = $customer->nombres;
            $this->customerID = $customer->id;
            $this->apellidos = $customer->apellidos;
            $this->telefono = $customer->telefono;
            $this->totalCreditos = $creditos->count();
        } else {
            $this->id_seleccionado = 0;
        }
    }

    public function obtenerDatos()
    {
        $prestamos = Prestamos::where('tipo', $this->tipo_simulador)
            ->where('periodo_id', '!=', null)
            ->where('status', 'A')
            ->get();
        $this->opcionesPrestamo = $prestamos;
    }
    public function obtenerDatosCredito()
    {
        $prestamo = Prestamos::find($this->prestamo_simulador);
        $this->valor_ahorrar_credito = $prestamo->ahorro;
    }
    public function obtenerDatosNova()
    {
        $prestamos = Prestamos::where('tipo', $this->tipoNovaCredito)
            ->where('periodo_id', '!=', null)
            ->where('status', 'A')
            ->get();
        $this->opcionesPrestamo = $prestamos;
    }

    public function guardarCreditoNovacion()
    {
        $this->validate(
            [

                'cuotasNovaNuew' => 'required',
                'tipoNovaCredito' => 'required',
                'prestamoNova' => 'required',

            ],
            [

                'cuotasNovaNuew.required' => 'Necesita ingresar el numero de cuotas.',
                'tipoNovaCredito.required' => 'Necesita seleccionar una tabla.',
                'prestamoNova.required' => 'Necesita seleccionar una fecha para iniciar el prestamo.',

            ]
        );

        $this->valor_simulador = $this->valorprestamoNova;
        $this->cuotas_simulador = $this->cuotasNovaNuew;
        $this->tipo_simulador = $this->tipoNovaCredito;
        $this->fecha_prestamo = date('Y-m-d');
        $this->prestamo_simulador = $this->prestamoNova;
        $this->generar = 1;
        $this->estadoCreditoNovacion = true;
        $prestamos = Prestamos::find($this->prestamo_simulador);
        if ($prestamos->diario == 0) {
            $this->generarNormal();
        } else {
            $this->generarDiario();
            $this->diarioLetras = true;
        }
        if ($this->generar == 1) {
            $this->verBtnGenerar = false;
            $this->guardarCredito();
            $this->verBtnGenerar = false;
            $this->mensajeCreacion = 'EL CREDITO DE ' . $this->nombres . ' ' . $this->apellidos . ' SE A CREADO CORRECTAMENTE CON EL CODIGO ' . $this->codigoPrestamo;
        } else {
            $this->codigoPrestamo = 0;
        }
        $this->verTabla = 1;
        $this->verPdf = true;
        $this->generarDatos($this->customerID);
        $this->procesando = false;
        $this->estadoCreditoNovacion = false;
        $updateNOvacion = CreditFolderHeader::find($this->idActualizarAnteriorNovacion);
        $updateNOvacion->valor_novacion = 0;
        $updateNOvacion->save();
    }

    public function obtenerDatosFamilares()
    {
        if ($this->generar == 1) {
            $customerParentezco = CustomerParentezco::where('customer_id', $this->id_seleccionado)->get();
            $this->familiares = $customerParentezco;
        } else {
            $this->familiares = [];
        }
    }

    public function verificarGaranteCruzado()
    {
        if ($this->garante != '') {
            $creditos = CreditFolderHeader::where('customer_garante_id', $this->garante)->pluck('code');
            $valores = CreditFolderDetail::whereIn('code_folder_header', $creditos)
                ->where('status', 'PENDIENTE')
                ->sum('valor_cuota');
            $datosGarante = Customer::find($this->garante);

            if ($valores > 0) {
                $color = 'danger';
                $mensaje = $datosGarante->nombres . ' ' . $datosGarante->apellidos . ' No puede ser garante por que ya es garante de otro credito activo ';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                $this->pararTodo = 1;
                return;
            }
        }
    }

    public function validarEdad()
    {
        $customer = Customer::find($this->customerID);
        $prestamos = Prestamos::find($this->prestamo_simulador);
        if ($this->prestamo_simulador == '') {
            $color = 'danger';
            $mensaje = 'Seleccione un prestamo para continuar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->pararTodo = 1;
            return;
        }
        $edad = 0;
        if ($customer->fecha_nacimiento != null && $customer->fecha_nacimiento != '') {
            $fechaNacimiento = $customer->fecha_nacimiento;
            $fechaNacimiento = new DateTime($fechaNacimiento);
            $hoy = new DateTime();
            $edad = $hoy->diff($fechaNacimiento)->y;
        } else {
            $color = 'danger';
            $mensaje = 'Configure la fecha de nacimiento de ' . $customer->nombres . ' ' . $customer->nombres . ', para continuar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->pararTodo = 1;
            return;
        }
        if (!($edad >= $prestamos->edad_minima && $edad <= $prestamos->edad_maxima)) {
            $color = 'danger';
            $mensaje = 'No cumple con el requicito de edad para continuar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->pararTodo = 1;
            return;
        }
    }

    public function validarValorPrestamo()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $valorSolicitado = $this->valor_simulador;
        if (!($valorSolicitado >= $prestamos->valor_minimo && $valorSolicitado <= $prestamos->valor_maximo)) {
            $color = 'danger';
            $mensaje = 'No cumple con el rango de valores para este prestamo, el rango es desde ' . $prestamos->valor_minimo . '$ hasta ' . $prestamos->valor_maximo . '$';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->pararTodo = 1;
            return;
        }
    }

    public function validarEncajeCuenta()
    {
        $customer = Customer::find($this->customerID);
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $valorSolicitado = $this->valor_simulador;
        $cuentaDescargo = CustomerTipoAhorros::select('customer_tipo_ahorros.*')
            ->join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', 'tipo_ahorros.id')
            ->where('tipo_ahorros.descargo_creditos', true)
            ->where('customer_tipo_ahorros.company_id', Auth::user()->company_id)
            ->where('customer_tipo_ahorros.customer_id', $customer->id)
            ->first();

        if ($prestamos->encaje) {
            if ($prestamos->encaje_credito_cuenta == 'CUENTA') {
                if (!$cuentaDescargo) {
                    $color = 'danger';
                    $mensaje = 'No dispone de una cuenta de descargo para realizar la revisión de valores.';
                    $data = [
                        'titulo' => 'Notificación',
                        'color' => $color,
                        'mensaje' => $mensaje
                    ];
                    $this->dispatchBrowserEvent('alerta', $data);
                    $this->pararTodo = 1;
                    return;
                }

                $ingresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                    ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN'])
                    ->where('customer_historials.customer_tipo_ahorro_id', $cuentaDescargo->id)
                    ->where('customer_historials.status', true)
                    ->sum('customer_historials.valor_movimiento');
                $egresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                    ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN'])
                    ->where('customer_historials.customer_tipo_ahorro_id', $cuentaDescargo->id)
                    ->where('customer_historials.status', true)
                    ->sum('customer_historials.valor_movimiento');
                $valorCuenta = $ingresos - $egresos;

                switch ($prestamos->encaje_porcentaje_valor) {
                    case 'VALOR':
                        if ($prestamos->encaje_cantidad > $valorCuenta) {
                            $color = 'danger';
                            $mensaje = 'No dispone de los valores de encaje en su cuenta de descargos de creditos número: ' . $cuentaDescargo->codigo;
                            $data = [
                                'titulo' => 'Notificación',
                                'color' => $color,
                                'mensaje' => $mensaje
                            ];
                            $this->dispatchBrowserEvent('alerta', $data);
                            $this->pararTodo = 1;
                            return;
                        }
                        break;
                    case 'PORCENTAJE':
                        $porcentajeCredito = $this->valor_simulador * ($prestamos->encaje_cantidad / 100);
                        if ($porcentajeCredito > $valorCuenta) {
                            $color = 'danger';
                            $mensaje = 'No dispone de los valores de encaje en su cuenta de descargos de creditos número: ' . $cuentaDescargo->codigo;
                            $data = [
                                'titulo' => 'Notificación',
                                'color' => $color,
                                'mensaje' => $mensaje
                            ];
                            $this->dispatchBrowserEvent('alerta', $data);
                            $this->pararTodo = 1;
                            return;
                        }
                        break;
                }
            }
        }
    }

    public function verificarValorEncaje()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
        if (isset($prestamos) && $prestamos->encaje == true) {
            if ($prestamos->encaje_porcentaje_valor == 'PORCENTAJE') {
                $porcentajeCredito = $this->valor_simulador * ($prestamos->encaje_cantidad / 100);
                $this->valorEncajado = $porcentajeCredito;
            } else {
                $this->valorEncajado = $prestamos->encaje_cantidad;
            }
        }
    }

    public function simular()
    {
        $this->validate(
            [
                'valor_simulador' => 'required',
                'cuotas_simulador' => 'required',
                'tipo_simulador' => 'required',
                'fecha_prestamo' => 'required',
                'prestamo_simulador' => 'required',
                'generar' => 'required',
                'garante' => ($this->generar == 1 && $this->obligarGarante == 1) ? 'required' : '',
            ],
            [
                'valor_simulador.required' => 'Necesita ingresar un valor.',
                'cuotas_simulador.required' => 'Necesita ingresar el numero de cuotas.',
                'tipo_simulador.required' => 'Necesita seleccionar una tabla.',
                'fecha_prestamo.required' => 'Necesita seleccionar una fecha para iniciar el prestamo.',
                'prestamo_simulador.required' => 'Necesita seleccionar el prestamo que desea realizar.',
                'generar.required' => 'Necesita seleccionar si es simulador o un crédito real.',
                'garante.required' => 'Necesita seleccionar un garante para generar el crédito.',
            ]
        );
        if ($this->procesando) {
            // Si ya está en proceso, no hagas nada
            return;
        }
        $this->procesando = true;
        $this->valorCreditoSumado = $this->valor_simulador;

        $prestamos = Prestamos::find($this->prestamo_simulador);
        $this->valorCreditoSumado = 0.00;
        $this->valorCreditoSumadoPrimeraLetra = 0.00;
        if ($prestamos->suma_valores_gastos_prestamo) {
            if ($prestamos->letra_credito == 'LETRA') {
                $valoresGastosCalculo = BaseController::valoresGastosCalculo($prestamos, $this->valor_simulador);
                $this->valorCreditoSumadoPrimeraLetra = $valoresGastosCalculo['gasto1'] + $valoresGastosCalculo['gasto2'] + $valoresGastosCalculo['gasto3'];
                $this->valorCreditoSumado = 0;
                $color = 'info';
                $mensaje = 'Este crédito suma el valor de ' . $this->valorCreditoSumadoPrimeraLetra . ' $ por gastos administrativos al prestamo ' . $this->valor_simulador . " $ se sumaráa a la primera letra del crédito";
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
            } else {
                $this->valorCreditoSumadoPrimeraLetra = 0;
                if ($prestamos->administrativo_porcentaje_valor == "VALOR") {
                    $valorAdministrativo = $prestamos->gasto_administrativo;
                } else {
                    $solicitadoPrestamo = $this->valor_simulador;
                    $valorAdministrativo = $solicitadoPrestamo * ($prestamos->gasto_administrativo / 100);
                }

                $valoresGastosCalculo = BaseController::valoresGastosCalculo($prestamos, $this->valor_simulador);


                $this->valorCreditoSumado = $valoresGastosCalculo['gasto1'] + $valoresGastosCalculo['gasto2'] + $valoresGastosCalculo['gasto3'];

                $color = 'info';
                $mensaje = 'Este crédito suma el valor de ' . $this->valorCreditoSumado . ' $ por gastos administrativos al prestamo ' . $this->valor_simulador . " $";
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
            }
        }

        if ($prestamos->calculo_simple) {
            $this->generarSimple();
        } else {
            if ($prestamos->diario == 0) {
                $this->generarNormal();
            } else {
                $this->generarDiario();
                $this->diarioLetras = true;
            }
        }
        if ($this->generar == 1) {
            $this->guardarCredito();
            $this->mensajeCreacion = 'EL CREDITO DE ' . $this->nombres . ' ' . $this->apellidos . ' SE A CREADO CORRECTAMENTE CON EL CODIGO ' . $this->codigoPrestamo;
        } else {
            $this->codigoPrestamo = 0;
        }
        $this->verTabla = 1;
        $this->verPdf = true;
        $this->generarDatos($this->customerID);
        $this->procesando = false;
    }

    public function generarNormal()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $customer = Customer::find($this->customerID);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $this->fecha_prestamo;
        $interes = $prestamos->interes;
        $deuda = $this->valor_simulador + $this->valorCreditoSumado;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        switch ($recuTiempo) {
            case 'D':
                if ($prestamos->tipo == 'F') {

                    //$inte = ($interes / 100) / 12;
                    //$letrasMes = $this->cuotas_simulador / 24;
                    //$cuotas = ($deuda * $inte * (pow((1 + $inte), ($letrasMes)))) / ((pow((1 + $inte), ($letrasMes))) - 1);
                    //$fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $letrasMes), 2, '.', '');
                    //$valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    //$this->valorCuota = $valorCuotas / 24;
                    //$this->valorDesgravament = $fondoDesgravamen / 24;
                    //$cuotaNueva =  $cuotas / 24;
                    //for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                    //    $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                    //    $periodo = $recurrencia->separacion;
                    //    $fechaMasDias = $fecha->addMonths($periodo);
                    //    $fechaPrestamo = $fechaMasDias->toDateString();
                    //    $amortizado =   ($deuda) - $cuotas;
                    //    $interes = number_format($deuda * $inte,  2, '.', '');
                    //    $sumaInteres = $sumaInteres + $interes;
                    //    $deuda = $deuda - ($cuotaNueva - ($deuda * $inte));
                    //    $pagos[] = [
                    //        'cuotas' => $i,
                    //        'fechas' => $fechaPrestamo,
                    //        'valMes' => number_format($valorCuotas / 24, 2, '.', ''),
                    //        'desgravamen' => number_format($fondoDesgravamen / 24, 2, '.', ''),
                    //        'deuda' => number_format($deuda, 2, '.', ''),
                    //        'cuotaPago' => number_format($valorCuotas / 24, 2, '.', ''),
                    //        'interes' => $interes,
                    //        'amoritizado' => number_format($amortizado, 2, '.', ''),
                    //    ];
                    //}
                    //$sumaInteres = ($sumaInteres / 24);

                    $inte = (($interes / 100) / 151);
                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
                    } else {
                        $cuotas = $deuda / $this->cuotas_simulador;
                    }


                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2, '.', '');
                    //$fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2);
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    $this->valorCuota = $valorCuotas;
                    $this->valorDesgravament = $fondoDesgravamen;
                    for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        if (!$fechaMasDias->isSunday()) {
                            $amortizado = $cuotas - ($deuda * $inte);
                            $interes = number_format(($deuda * $inte), 2, '.', '');
                            //$interes = number_format($deuda * $inte, 2);
                            $sumaInteres = $sumaInteres + $interes;
                            $deuda = $deuda - ($cuotas - ($deuda * $inte));
                            $pagos[] = [
                                'cuotas' => $i,
                                'fechas' => $fechaPrestamo,
                                'valMes' => $valorCuotas,
                                'desgravamen' => $fondoDesgravamen,
                                'deuda' => number_format($deuda, 2, '.', ''),
                                'cuotaPago' => $valorCuotas,
                                'interes' => $interes,
                                //'amoritizado' => number_format($amortizado, 2, '.', ''),
                                'amoritizado' => number_format($valorCuotas - $fondoDesgravamen - $interes, 2, '.', ''),
                            ];
                        } else {
                            $i--;
                        }
                    }
                } else {
                    $prestamo = $deuda; // cantidad del préstamo
                    $interes_anual = $interes; // tasa de interés anual
                    $plazo = $this->cuotas_simulador; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / 12; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    $this->valorCuota = 0;
                    $this->valorDesgravament = $fondo_desgravamen;
                    for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $valInteres = $dineroCalculo * $interes_mensual;
                        $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                        $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                        $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                        $sumaInteres = $sumaInteres + $valInteres;
                        $dineroCalculo = $dineroCalculo - $amortizado;
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'desgravamen' => number_format($fondo_desgravamen, 2, '.', ''),
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {

                    //$inte = ($interes / 100) / 12;
                    //$letrasMes = $this->cuotas_simulador / 4;
                    //$cuotas = ($deuda * $inte * (pow((1 + $inte), ($letrasMes)))) / ((pow((1 + $inte), ($letrasMes))) - 1);
                    //$fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $letrasMes), 2, '.', '');
                    //$valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    //$this->valorCuota = $valorCuotas / 4;
                    //$this->valorDesgravament = $fondoDesgravamen / 4;
                    //$cuotaNueva =  $cuotas / 4;
                    //for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                    //    $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                    //    $periodo = $recurrencia->separacion;
                    //    $fechaMasDias = $fecha->addMonths($periodo);
                    //    $fechaPrestamo = $fechaMasDias->toDateString();
                    //    $amortizado =   ($deuda) - $cuotas;
                    //    $interes = number_format($deuda * $inte,  2, '.', '');
                    //    $sumaInteres = $sumaInteres + $interes;
                    //    $deuda = $deuda - ($cuotaNueva - ($deuda * $inte));
                    //    $pagos[] = [
                    //        'cuotas' => $i,
                    //        'fechas' => $fechaPrestamo,
                    //        'valMes' => number_format($valorCuotas / 4, 2, '.', ''),
                    //        'desgravamen' => number_format($fondoDesgravamen / 4, 2, '.', ''),
                    //        'deuda' => number_format($deuda, 2, '.', ''),
                    //        'cuotaPago' => number_format($valorCuotas / 4, 2, '.', ''),
                    //        'interes' => $interes,
                    //        'amoritizado' => number_format($amortizado, 2, '.', ''),
                    //    ];
                    //}
                    //$sumaInteres = ($sumaInteres / 4);


                    // $letrasTotal = $this->cuotas_simulador / 4;
                    $inte = ($interes / 100) / 36;

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
                    } else {
                        $cuotas = $deuda / $this->cuotas_simulador;
                    }

                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2, '.', '');
                    //$fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2);
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    $this->valorCuota = $valorCuotas;
                    $this->valorDesgravament = $fondoDesgravamen;
                    for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addWeeks($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format(($deuda * $inte) / 4, 2, '.', '');
                        //$interes = number_format($deuda * $inte, 2);
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => $valorCuotas,
                            'desgravamen' => $fondoDesgravamen,
                            'deuda' => number_format($deuda, 2, '.', ''),
                            'cuotaPago' => $valorCuotas,
                            'interes' => $interes,
                            //'amoritizado' => number_format($amortizado, 2),
                            'amoritizado' => number_format($valorCuotas - $fondoDesgravamen - $interes, 2, '.', ''),
                        ];
                    }
                } else {
                    $prestamo = $deuda; // cantidad del préstamo
                    $interes_anual = $interes; // tasa de interés anual
                    $plazo = $this->cuotas_simulador; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / 12; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    $this->valorCuota = 0;
                    $this->valorDesgravament = $fondo_desgravamen;
                    for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addWeeks($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $valInteres = $dineroCalculo * $interes_mensual;
                        $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                        $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                        $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                        $sumaInteres = $sumaInteres + $valInteres;
                        $dineroCalculo = $dineroCalculo - $amortizado;
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'desgravamen' => number_format($fondo_desgravamen, 2, '.', ''),
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
            case 'M':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / 12;

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
                    } else {
                        $cuotas = $deuda / $this->cuotas_simulador;
                    }

                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2, '.', '');
                    //$fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2);
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    $this->valorCuota = $valorCuotas;
                    $this->valorDesgravament = $fondoDesgravamen;
                    for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addMonths($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2, '.', '');
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => $valorCuotas,
                            'desgravamen' => $fondoDesgravamen,
                            'deuda' => number_format($deuda, 2, '.', ''),
                            'cuotaPago' => $valorCuotas,
                            'interes' => $interes,
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                } else {
                    $prestamo = $deuda; // cantidad del préstamo
                    $interes_anual = $interes; // tasa de interés anual
                    $plazo = $this->cuotas_simulador; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / 12; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    $this->valorCuota = 0;
                    $this->valorDesgravament = $fondo_desgravamen;
                    for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addMonths($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $valInteres = $dineroCalculo * $interes_mensual;
                        $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                        $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                        $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                        $sumaInteres = $sumaInteres + $valInteres;
                        $dineroCalculo = $dineroCalculo - $amortizado;
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'desgravamen' => number_format($fondo_desgravamen, 2, '.', ''),
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
        }

        $this->interesSuma = number_format($sumaInteres, 2, '.', '');
        $this->listaLetras = $pagos;
    }

    public function generarDiario()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $customer = Customer::find($this->customerID);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $this->fecha_prestamo;
        $fechaPrestamoDiarios = $this->fecha_prestamo;
        $interes = $prestamos->interes;
        $deuda = $this->valor_simulador + $this->valorCreditoSumado;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        $divider = 30;
        switch ($recuTiempo) {
            case 'D':
                if ($prestamos->tipo == 'F') {
                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $this->cuotas_simulador / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2, '.', ''); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $this->cuotas_simulador, 2, '.', '');
                    $cuotaPago = number_format($valorFinal / $this->cuotas_simulador, 2, '.', ''); //valor de la cuota
                    $contador = 1;

                    while ($contador <= $this->cuotas_simulador) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamo;

                        if (date("w", strtotime($fecha)) != 0) {

                            $pagos[] = [
                                'cuotas' => $contador,
                                'fechas' => $fechaPrestamo,
                                'valMes' => $cuotaPago,
                                'desgravamen' => 0,
                                'deuda' => 0,
                                'cuotaPago' => $cuotaPago,
                                'interes' => $interesPorLetra,
                                'amoritizado' => number_format(0, 2),
                            ];
                            $contador++;
                        }
                    }
                } else {
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {
                    $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                    $fechaMasDias = $fecha->addWeeks($this->cuotas_simulador);
                    $fchaNueva = $fechaMasDias->toDateString();
                    //                    cantidad de diase netre las fechas
                    $fecha1 = Carbon::parse($fechaPrestamo);
                    $fecha2 = Carbon::parse($fchaNueva);

                    $diferenciaEnDias = $fecha1->diffInDays($fecha2);

                    $contador = 1;
                    $diasRestar = 0;
                    $diasSumar = 0;
                    while ($contador <= $diferenciaEnDias) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamo;
                        if (date("w", strtotime($fecha)) != 0) {
                            $diasSumar += 1;
                        } else {
                            $diasRestar += 1;
                        }
                        $contador++;
                    }
                    $recorrido = $diasSumar;

                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $recorrido / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2, '.', ''); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $recorrido, 2, '.', '');
                    $cuotaPago = number_format($valorFinal / $recorrido, 2, '.', ''); //valor de la cuota
                    $contadorLetras = 1;
                    while ($contadorLetras <= $recorrido) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamoDiarios);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamoDiarios = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamoDiarios;
                        if (date("w", strtotime($fecha)) != 0) {
                            $pagos[] = [
                                'cuotas' => $contadorLetras,
                                'fechas' => $fechaPrestamoDiarios,
                                'valMes' => $cuotaPago,
                                'desgravamen' => 0,
                                'deuda' => 0,
                                'cuotaPago' => $cuotaPago,
                                'interes' => $interesPorLetra,
                                'amoritizado' => number_format(0, 2),
                            ];
                            $contadorLetras++;
                        }
                    }
                } else {
                }
                break;
            case 'M':
                if ($prestamos->tipo == 'F') {
                    $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                    $fechaMasDias = $fecha->addMonths($this->cuotas_simulador);
                    $fchaNueva = $fechaMasDias->toDateString();
                    //                    cantidad de diase netre las fechas
                    $fecha1 = Carbon::parse($fechaPrestamo);
                    $fecha2 = Carbon::parse($fchaNueva);
                    $diferenciaEnDias = $fecha1->diffInDays($fecha2);
                    $contador = 1;
                    $diasSumar = 0;
                    $diasRestar = 0;
                    while ($contador <= $diferenciaEnDias) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamo;
                        if (date("w", strtotime($fecha)) != 0) {
                            $diasSumar += 1;
                        } else {
                            $diasRestar += 1;
                        }
                        $contador++;
                    }
                    $recorrido = $diasSumar;
                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $recorrido / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2, '.', ''); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $recorrido, 2, '.', '');
                    $cuotaPago = number_format($valorFinal / $recorrido, 2, '.', ''); //valor de la cuota
                    $contadorLetras = 1;
                    while ($contadorLetras <= $recorrido) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamoDiarios);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamoDiarios = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamoDiarios;
                        if (date("w", strtotime($fecha)) != 0) {
                            $pagos[] = [
                                'cuotas' => $contadorLetras,
                                'fechas' => $fechaPrestamoDiarios,
                                'valMes' => $cuotaPago,
                                'desgravamen' => 0,
                                'deuda' => 0,
                                'cuotaPago' => $cuotaPago,
                                'interes' => $interesPorLetra,
                                'amoritizado' => number_format(0, 2),
                            ];
                            $contadorLetras++;
                        }
                    }
                } else {
                }
                break;
        }
        $this->interesSuma = $interesLetras;
        $this->listaLetras = $pagos;
    }

    public function generarSimple()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $customer = Customer::find($this->customerID);
        $pagos = array();
        $fechaPrestamo = $this->fecha_prestamo;
        $interes = $prestamos->interes;
        $deuda = $this->valor_simulador + $this->valorCreditoSumado;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        $letras = $this->cuotas_simulador;
        $inte = (($deuda * ($interes / 100)) / 12);
        $valorCapAmortizado = ($deuda + ($inte * $letras)) / $letras;

        $cuotas = (($deuda + ($inte * $letras)) / $letras) - $inte;
        $fondoDesgravamen = 0;
        $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
        $this->valorCuota = $valorCuotas;
        $this->valorDesgravament = $fondoDesgravamen;
        for ($i = 1; $i <= $this->cuotas_simulador; $i++) {
            $fecha = \Carbon\Carbon::parse($fechaPrestamo);
            $periodo = 1;
            $fechaMasDias = $fecha->addMonths($periodo);
            $fechaPrestamo = $fechaMasDias->toDateString();
            $amortizado = $valorCapAmortizado;
            $interes = number_format($inte, 2, '.', '');
            $sumaInteres = $sumaInteres + $interes;
            $deuda = $deuda - ($cuotas);
            $pagos[] = [
                'cuotas' => $i,
                'fechas' => $fechaPrestamo,
                'valMes' => $valorCapAmortizado,
                'desgravamen' => $fondoDesgravamen,
                'deuda' => number_format($deuda, 2, '.', ''),
                'cuotaPago' => number_format($valorCapAmortizado, 2, '.', ''),
                'interes' => number_format($inte, 2, '.', ''),
                'amoritizado' => number_format($valorCuotas, 2, '.', ''),
            ];
        }

        $this->interesSuma = number_format($sumaInteres, 2, '.', '');
        $this->listaLetras = $pagos;
    }

    public function guardarCredito()
    {
        $this->pararTodo = 0;
        $this->validarEdad();
        $this->validarValorPrestamo();
        $this->validarEncajeCuenta();
        $this->verificarValorEncaje();
        $this->verificarGaranteCruzado();

        if ($this->pararTodo == 0) {
            $customer = Customer::find($this->customerID);
            $garanteDato = Customer::find($this->garante);
            $prestamos = Prestamos::find($this->prestamo_simulador);
            $valorAdministrativo = 0;
            if ($prestamos->administrativo_porcentaje_valor == "VALOR") {
                $valorAdministrativo = $prestamos->gasto_administrativo;
            } else {
                $solicitadoPrestamo = $this->valor_simulador;
                $valorAdministrativo = $solicitadoPrestamo * ($prestamos->gasto_administrativo / 100);
            }
            $tabla = 'credit_folder_headers';
            //$code = BaseController::generarCodigo($tabla, 3);
            $codeString = BaseController::generarCodigoPrestamos($tabla, 6);
            $this->codeString = $codeString;
            $valoresGastosCalculo = BaseController::valoresGastosCalculo($prestamos, $this->valor_simulador);
            // Crear el atributo de cuenta contable basado en los campos de cuotas y prestamo
            $plazoMeses = $this->cuotas_simulador;
            $plazoEnDias = $plazoMeses * 30;
            $cuentaMadre = PlanCuentas::find($prestamos->plan_cuenta_id);
            $cuentaContableFinalId = null;
            if ($cuentaMadre) {
                $codigoMadre = $cuentaMadre->codigo;
                $codigoHija = '';
                if ($plazoEnDias <= 30) {
                    $codigoHija = $codigoMadre . '.05';
                } elseif ($plazoEnDias <= 90) {
                    $codigoHija = $codigoMadre . '.10';
                } elseif ($plazoEnDias <= 180) {
                    $codigoHija = $codigoMadre . '.15';
                } elseif ($plazoEnDias <= 360) {
                    $codigoHija = $codigoMadre . '.20';
                } else {
                    $codigoHija = $codigoMadre . '.25';
                }
                $cuentaHija = PlanCuentas::where('codigo', $codigoHija)
                    ->where('company_id', Auth::user()->company_id)
                    ->first();
                $cuentaContableFinalId = $cuentaHija ? $cuentaHija->id : null;
            }

            if ($cuentaContableFinalId == null) {
                $this->addError('cuenta_contable', 'El tipo de préstamo no tiene configurada una cuenta contable válida para este plazo. Verifique la parametrización (Plan de Cuentas).');
                $this->pararTodo = 1;
                return;
            }

            $dataHeader = [
                "company_id" => Auth::user()->company_id,
                "code" => $codeString,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "customer_ruc" => $customer->numero_documento,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_phone" => $customer->telefono,
                "customer_address" => $customer->direccion,
                "customer_email" => $customer->correo,
                "valor_solicitado" => $this->valor_simulador,
                "suma_valores_gastos_prestamo" => $this->valorCreditoSumado,
                "anios_pagar" => $this->cuotas_simulador / 12,
                "cuotas_pagar" => $this->cuotas_simulador,
                "valor_cuota" => $this->valorCuota,
                "cuenta_contable_id" => $cuentaContableFinalId,
                "valor_desgravamen" => $this->valorDesgravament,
                "date_created" => $this->fecha_prestamo,
                "hour_created" => date("H:i:s"),
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "tipo_prestamo" => $prestamos->id,
                "administrativo_porcentaje_valor" => $prestamos->administrativo_porcentaje_valor,
                "gasto_administrativo" => $valorAdministrativo,
                "encaje" => $prestamos->encaje,
                "encaje_credito_cuenta" => ($prestamos->encaje == 1) ? $prestamos->encaje_credito_cuenta : null,
                "encaje_porcentaje_valor" => ($prestamos->encaje == 1) ? $prestamos->encaje_porcentaje_valor : null,
                "encaje_cantidad" => ($prestamos->encaje == 1) ? $prestamos->encaje_cantidad : 0,
                "encaje_valor" => ($prestamos->encaje == 1) ? $this->valorEncajado : 0,
                "primer_gasto" => $valoresGastosCalculo['gasto1'],
                "segundo_gasto" => $valoresGastosCalculo['gasto2'],
                "tercer_gasto" => $valoresGastosCalculo['gasto3'],
                "porcentaje_primer_gasto" => $valoresGastosCalculo['porcentage1'],
                "porcentaje_segundo_gasto" => $valoresGastosCalculo['porcentage2'],
                "porcentaje_tercer_gasto" => $valoresGastosCalculo['porcentage3'],
                "ahorro" => $this->valor_ahorrar_credito,
                "porcentaje_interes_prestamo" => $prestamos->interes,
            ];
            $createHeader = CreditFolderHeader::create($dataHeader);
            $this->cabeceraCreada = $createHeader->id;
            foreach ($this->garantesArreglo as $value) {
                $dataGarante = [
                    'company_id' => Auth::user()->company_id,
                    'credit_folder_headers_id' => $createHeader->id,
                    'customer_id' => $value['customerId'],
                    'customer_name' => $value['customerName'],
                    'customer_identificacion' => $value['customerIdentificacion'],
                    'customer_conyuge_name' => $value['customerConyugeNombre'],
                    'customer_conyuge_identificacion' => $value['customerConyugeIdentificacion'],
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:i:s'),
                    'user_created_id' => Auth::user()->id,
                    'user_created_name' => Auth::user()->username
                ];
                Garantes::create($dataGarante);
            }

            if ($this->estadoCreditoNovacion == 1) {
                $updateNOvacion = CreditFolderHeader::find($createHeader->id);
                $updateNOvacion->status = 'ENTREGADO';
                $updateNOvacion->save();
            }
            $this->codigoPrestamo = $createHeader->code;
            foreach ($this->listaLetras as $letras) {
                $dataDetalle = [
                    "numero_cuota" => $letras['cuotas'],
                    "company_id" => Auth::user()->company_id,
                    "code_folder_header" => $createHeader->code,
                    "date_created" => $this->fecha_prestamo,
                    "hour_created" => date("H:i:s"),
                    "date_vencimiento" => $letras['fechas'],
                    "interes_periodo" => $letras['interes'],
                    "capital_amortizado" => $letras['amoritizado'],
                    "fondo_desgravamen" => $letras['desgravamen'],
                    "valor_cuota" => $letras['valMes'],
                    "saldo_remanente" => $letras['deuda'],
                ];
                CreditFolderDetail::create($dataDetalle);
            }
            $this->verBtnGenerar = false;
            $this->verBtnGenerar = false;
            if ($this->valorCreditoSumadoPrimeraLetra > 0) {
                $primeraLetra = CreditFolderDetail::where('code_folder_header', $createHeader->code)->first();
                $letraValor = $primeraLetra->valor_cuota + $this->valorCreditoSumadoPrimeraLetra;
                $primeraLetra->valor_gasto = $this->valorCreditoSumadoPrimeraLetra;
                $primeraLetra->valor_cuota = $letraValor;
                $primeraLetra->save();
            }
        }
    }

    public function generatePdf()
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
        $man = file_get_contents(public_path($path));
        $data['company'] = $company;
        $data['imagen'] = base64_encode($man);
        $data['detalle'] = $this->listaLetras;

        return PDF::loadView('creditos.pdf_creditos_generar', compact('data'))
            ->download('Credito.pdf');
    }
    public function exportarContrato($id)
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
        // Genera los datos que quieras mostrar en el PDF
        $data['cabecera'] = CreditFolderHeader::find($id);
        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['cabecera']->valor_solicitado, 2, 'DÓLARES', 'CENTAVOS');
        $data['garante'] = Customer::find($data['cabecera']->customer_garante_id);
        $data['interesNominal'] = 0;
        $prestamoTipo = Prestamos::find($data['cabecera']->tipo_prestamo);
        if ($prestamoTipo) {
            $data['interesNominal'] = $prestamoTipo->interes;
        }
        // Renderiza la vista y convierte en PDF
        $pdf = PDF::loadView('reportes.exportarContrato', $data);

        // Devuelve el PDF como descarga
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'CONTRATO DE PRESTAMO.pdf');
    }

    public function cargarDatosPrestamo($creditId)
    {
        $this->verCuentaAhorroPrestamo = false;
        $header = CreditFolderHeader::find($creditId);
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $header->code)->get();
        $contPendientes = 0;
        foreach ($detalle as $value) {
            $value->pago_general = (float) round(($value->valor_cuota + $value->faltante_anterior_cuota + $value->interes_mora - $value->saldo_anterior_cuota), 2);
            if ($value->status == 'PENDIENTE') {
                if ($contPendientes == 0) {
                    $value->armado = true;
                    $contPendientes++;
                } else {
                    $value->armado = false;
                }
            }
        }
        $this->headerPago = $header->id;
        $this->headerPagoCode = $header->code;
        $this->detallePago = $detalle;
        $this->mensaje = '';
    }

    public function cargarDatosNovacion($creditId)
    {
        $sumaInteresPeriodo = 0;
        $sumaInteresMora = 0;
        $sumaFondoDesgravamen = 0;
        $sumaAmortizado = 0;
        $sumaInteresPeriodoCumplido = 0;
        $sumaInteresMoraCumplido = 0;
        $sumaFondoDesgravamenCumplido = 0;
        $this->generarInteresMora($creditId);
        $header = CreditFolderHeader::find($creditId);
        $this->creditoIdnovacion = $creditId;
        $this->creditoIdnovacionDatos = $header->code;
        //pendientes
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('date_vencimiento', '<', date('Y-m-d'))
            ->where('status', 'PENDIENTE')
            ->get();
        $sumaInteresPeriodo = $detalle->sum('interes_periodo');
        $sumaInteresMora = $detalle->sum('interes_mora');
        $sumaFondoDesgravamen = $detalle->sum('fondo_desgravamen');
        $this->valorVencido = number_format($sumaInteresPeriodo + $sumaInteresMora + $sumaFondoDesgravamen, 2);
        //amortizado
        $amortizado = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            //->where('date_vencimiento', '>', date('Y-m-d'))
            ->where('status', 'PENDIENTE')
            ->get();
        $sumaAmortizado = $amortizado->sum('capital_amortizado');
        $this->valorCapitalAmortizado = number_format($sumaAmortizado, 2);
        //cumplido
        $cumplido = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('date_vencimiento', date('Y-m-d'))
            ->where('status', 'PENDIENTE')
            ->get();
        $sumaInteresPeriodoCumplido = $cumplido->sum('interes_periodo');
        $sumaInteresMoraCumplido = $cumplido->sum('interes_mora');
        $sumaFondoDesgravamenCumplido = $cumplido->sum('fondo_desgravamen');
        $this->valorCumplido = number_format($sumaInteresPeriodoCumplido + $sumaInteresMoraCumplido + $sumaFondoDesgravamenCumplido, 2);
    }

    public function guardarNovacion()
    {
        $totalLiquidar = 0;
        $header = CreditFolderHeader::find($this->creditoIdnovacion);
        $header->status = 'NOVACION';
        $totalLiquidar = $this->valorVencido + $this->valorCumplido;
        $header->valor_liquidar = $totalLiquidar;
        $header->valor_novacion = $this->valorCapitalAmortizado;
        $header->save();
    }

    public function novacionNotificar($id)
    {
        $cedito = CreditFolderHeader::find($id);
        $data = [
            'cedito' => $cedito,
        ];
        $this->dispatchBrowserEvent('notificarAccion', $data);
    }

    public function pagarnovacion($id)
    {
        $header = CreditFolderHeader::find($id);
        //$header->status = 'PAGADO';
        //$header->save();
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($detalle as $val) {
            $detalle = CreditFolderDetail::find($val->id);
            $detalle->date_pay = date('Y-m-d');
            $detalle->hour_pay = date('H:i:s');
            $detalle->user_pay_id = Auth::user()->id;
            $detalle->status = 'PAGADA';
            $detalle->obervation_pago = 'NOVACION';
            $detalle->save();
        }
        $customer = Customer::find($header->customer_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'NOV')
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
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $header->valor_liquidar,
            "saldo_general" => $valorTotal + $header->valor_liquidar,
            "observation" => 'PAGO NOVACION DEL CREDITO ' . $header->code . ' ; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $header->valor_liquidar, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
    }

    public function notificacionReversoCredito($id)
    {
        $cedito = CreditFolderHeader::find($id);
        $data = [
            'cedito' => $cedito,
        ];
        $this->dispatchBrowserEvent('notificarAccionReversoCredito', $data);
    }

    public function reversarCredito($id)
    {
        $header = CreditFolderHeader::find($id);
        $header->status = 'PENDIENTE';
        $header->save();
    }

    public function generarNuevoNovacion($id)
    {
        $header = CreditFolderHeader::find($id);
        $this->valorprestamoNova = $header->valor_novacion;
        $this->idActualizarAnteriorNovacion = $id;
    }

    public function generarInteresMora($id)
    {
        $header = CreditFolderHeader::find($id);
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->where('date_vencimiento', '<', date('Y-m-d'))
            ->get();
        $company = Company::find(Auth::user()->company_id);
        foreach ($vencidas as $key => $value) {
            $fechaPagoInicial = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaPago = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaProrroga = $fechaPagoInicial->addDays($company->dias_inicio_cobro);
            $fechaProrrogaTrabajo = $fechaPago->addDays($company->dias_inicio_cobro);
            $fechaActual = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $fechaActualTrabajo = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $diasDebeFechaActual = $fechaProrrogaTrabajo->diffInDays($fechaActualTrabajo);
            if ($company->select_tipo_interes == 'D') {
                $diasTope = $company->numero_dias_interes;
                $diasReales = 0;
                if ($diasDebeFechaActual > 0) {
                    if ($diasDebeFechaActual > $company->dias_inicio_cobro) {
                        $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                    } else {
                        $diasReales = $diasDebeFechaActual;
                    }
                }
                if ($diasReales > $diasTope) {
                    $diasAdd = $diasTope - $company->dias_gracia;
                } else {
                    $diasAdd = $diasReales;
                }
            } else if ($company->select_tipo_interes == 'M') {
                $diasTope = $fechaProrrogaTrabajo->endOfMonth();
                $diferencia = $fechaProrroga->diff($diasTope);
                $diasReales = (int) $diferencia->format("%d");
                if ($diasReales > 0) {
                    $diasAdd = $diasReales + $company->dias_inicio_cobro - $company->dias_gracia;
                } else {
                    $diasAdd = 0;
                }
            } else if ($company->select_tipo_interes == 'S') {
                $fechaLetraInicio = $value->date_vencimiento;
                $code1 = $value->code_folder_header;
                if (isset($vencidas[$key + 1])) {
                    $siguienteValor = $vencidas[$key + 1];
                    $code2 = $siguienteValor->code_folder_header;
                    $fechaLetraFin = $siguienteValor->date_vencimiento;

                    if ($code1 == $code2) {
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $fechaLetraFin);
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;
                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        if ($fechaLetraInicio != date('Y-m-d')) {
                            $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                            $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                            $diferenciaDias = $fecha1->diffInDays($fecha2);
                            $diasReales = 0;
                            if ($diasDebeFechaActual > 0) {
                                $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                            }
                            if ($diasReales > $diferenciaDias) {
                                $diasAdd = $diferenciaDias - $company->dias_gracia;
                            } else {
                                $diasAdd = $diasReales;
                            }
                        } else {
                            $diasAdd = 0;
                        }
                    }
                } else {
                    if ($fechaLetraInicio != date('Y-m-d')) {

                        $fecha1 = Carbon::createFromFormat('Y-m-d', $fechaLetraInicio);
                        $fecha2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $diferenciaDias = $fecha1->diffInDays($fecha2);
                        $diasReales = 0;

                        if ($diasDebeFechaActual > 0) {
                            $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                        }
                        if ($diasReales > $diferenciaDias) {
                            $diasAdd = $diferenciaDias - $company->dias_gracia;
                        } else {
                            $diasAdd = $diasReales;
                        }
                    } else {
                        $diasAdd = 0;
                    }
                }
            }
            if ($company->select_tipo_interes == 'D' || $company->select_tipo_interes == 'M' || $company->select_tipo_interes == 'S') {
                $value->dias_mora = $diasAdd;
                $valor = $company->porcentaje_mora;
                $intervalMeses = (int) $diasAdd;
                $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                $value->interes = $porcentaje;
            } else {
                $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
                $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                $value->dias_mora = $date1->diffInDays($date2);
                $valor = $company->porcentaje_mora;
                $interval = $date2->diff($date1);
                $intervalMeses = (int) $interval->format("%m");
                $porcentaje = (float) round(($value->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                $value->interes = $porcentaje;
            }
            $letra = CreditFolderDetail::find($value->id);
            $letra->interes_mora = $porcentaje;
            $letra->save();
        }
    }

    public function consultarDatosLetra($id)
    {
        $this->gasto_cobranza = 0;
        $this->valor_ahorro = 0;
        $this->formasPagarCredito = [];
        //$valorMora = BaseController::calculoInteresMoraLetraValor($id);
        $valorMora = BaseController::calculoInteresMoraLetraValorMensual($id);
        $cuotamostrar = CreditFolderDetail::find($id);
        $cabecera = CreditFolderHeader::where('code', $cuotamostrar->code_folder_header)->first();
        $this->resumenLetra = $cuotamostrar;
        $this->numeroDeLetra = $cuotamostrar->numero_cuota;
        $this->idDeLetra = $cuotamostrar->id;
        $this->fechaPagoLetra = date('Y-m-d');
        $this->interesMora = $valorMora;
        $this->valor_ahorro = $cabecera->ahorro;
        $this->valorPagarLetra = number_format($cuotamostrar->valor_cuota + $this->valor_ahorro + $cuotamostrar->faltante_anterior_cuota + $valorMora - $cuotamostrar->saldo_anterior_cuota, 2);
        $this->generarListadeCreditos();
        $this->mensaje = '';
        $valorRemanete = '';
        if ($cuotamostrar->faltante_anterior_cuota > 0) {
            $valorRemanete .= 'Tiene un valor faltante de: <b>' . $cuotamostrar->faltante_anterior_cuota . '</b>';
        }
        if ($cuotamostrar->saldo_anterior_cuota > 0) {
            $valorRemanete .= 'Tiene un valor a favor de: <b>' . $cuotamostrar->saldo_anterior_cuota . '</b>';
        }
        $existeAhorro = ($this->valor_ahorro > 0) ? 'El ahorro del prestamo es : <b>' . $this->valor_ahorro . '</b>' : '';
        $this->descLetra = 'Nota: El valor de la letra es: <b>' . $cuotamostrar->valor_cuota . '</b> .El interes de mora es : <b>' . $valorMora . '</b> ' . $valorRemanete . $existeAhorro . ' Total a pagar : <b>' . $this->valorPagarLetra . '</b>';
        $company = Company::find(Auth::user()->company_id);
        if ($company->genera_gastos_cobranza == 1) {
            $this->gasto_cobranza = $company->valor_notificado * $cuotamostrar->notificado;
        } else {
            $this->gasto_cobranza = 0;
        }
        $this->cargarDatosPrestamo($this->headerPago);
    }

    public function actualizarValorPagar()
    {
        if ($this->idDeLetra != '') {
            $cuotamostrar = CreditFolderDetail::find($this->idDeLetra);
            $gastoCobranza = ($this->gasto_cobranza != '') ? $this->gasto_cobranza : 0;
            $this->valorPagarLetra = number_format($gastoCobranza + $cuotamostrar->valor_cuota + $cuotamostrar->faltante_anterior_cuota + $this->interesMora - $cuotamostrar->saldo_anterior_cuota, 2);
            $valorRemanete = '';
            if ($cuotamostrar->faltante_anterior_cuota > 0) {
                $valorRemanete .= 'Tiene un valor faltante de: <b>' . $cuotamostrar->faltante_anterior_cuota . '</b>';
            }
            if ($cuotamostrar->saldo_anterior_cuota > 0) {
                $valorRemanete .= 'Tiene un valor a favor de: <b>' . $cuotamostrar->saldo_anterior_cuota . '</b>';
            }
            $this->descLetra = 'Nota: USTED MODIFICO EL INTERES DE MORA. El valor de la letra es: <b>' . $cuotamostrar->valor_cuota . '</b> .El interes de mora es : <b>' . $this->interesMora . '</b> ' . $valorRemanete . ' Total a pagar : <b>' . $this->valorPagarLetra . '</b>';
        }
    }

    public function ultimaLetraPago($id)
    {
        $detalle = CreditFolderDetail::find($id);
        if ($detalle->saldo_remanente == "0.00") {
            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('code', $detalle->code_folder_header)
                ->first();
            //            $header->status = "FINALIZADO";
            $header->save();
        }
    }

    public function faltanteLetra($id, $valor)
    {
        $FALTANTE = (float) round($valor, 2);
        $detalle = CreditFolderDetail::find($id);
        $detalle->faltante_anterior_cuota = $FALTANTE;
        $detalle->save();
    }

    public function adelantarLetra($id, $valor)
    {
        $detalle = CreditFolderDetail::find($id);
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

    public function realizarPago()
    {
        $this->verCuentaAhorroPrestamo = false;
        if (count($this->formasPagarCredito) == 0) {
            $color = 'danger';
            $mensaje = 'Primero debe agregar formas de pago';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $pagoNormal = true;

        foreach ($this->formasPagarCredito as $formasPago) {
            if ($pagoNormal == true and $formasPago['formaName'] == 'TRANSFERENCIA') {
                $pagoNormal = false;
            }
        }
        if ($pagoNormal  == false) {
            $this->guardarFormasPagoPendientes($this->formasPagarCredito, $this->idDeLetra);
            return;
        }

        $cajaAbierta = Cajas::where('status', 'ABIERTA')
            ->where('date_inicial', date('Y-m-d'))
            ->where('user_inicial_id', Auth::user()->id)
            ->count();
        if ($cajaAbierta == 1) {
            $this->verBotonlistaPrestamos = true;
        } else {
            $this->verBotonlistaPrestamos = false;
        }
        if ($this->verBotonlistaPrestamos == false) {
            return;
        }

        $this->cargarDatosPrestamo($this->headerPago);
        if ($this->idDeLetra == "") {
            $this->mensaje = 'No puede realizar el pago si no selecciona una letra antes!';
        } else {
            $this->mensaje = '';
            $this->validate(
                [
                    'formaPago' => 'required',
                    'fechaPagoLetra' => 'required',
                    'valorPagarLetra' => 'required',
                ],
                [
                    'formaPago.required' => 'Debe Seleecionar una Forma de pago.',
                    'fechaPagoLetra.required' => 'Debe ingresar una fecha pare el pago.',
                    'valorPagarLetra.required' => 'Debe tener un valor a pagar',
                ]
            );

            $ahorrado = $this->valor_ahorro;
            $buscarDetalle = $this->idDeLetra;
            if ($ahorrado > 0) {
                $respuestaAhorro = $this->depositarAhorro($buscarDetalle);
                if ($respuestaAhorro == false) {
                    $this->verCuentaAhorroPrestamo = true;
                    $color = 'danger';
                    $mensaje = 'No tiene una cuenta de ahorro de prestamos configurada';
                    $data = [
                        'titulo' => 'Notificación',
                        'color' => $color,
                        'mensaje' => $mensaje
                    ];
                    $this->dispatchBrowserEvent('alerta', $data);
                    return;
                }
            }
            $valorPagado = 0;
            foreach ($this->formasPagarCredito as $formasPago) {
                $valorPagado += $formasPago['valor'];
            }
            $cuota = CreditFolderDetail::find($this->idDeLetra);
            $this->ultimaLetraPago($this->idDeLetra);
            $pagoTotal = (float) round(($cuota->valor_cuota + $this->interesMora + $cuota->faltante_anterior_cuota - $cuota->saldo_anterior_cuota), 2);
            $valorEnvio = (float) round($valorPagado - $this->gasto_cobranza - $ahorrado, 2);
            if ($valorEnvio == $pagoTotal) {
                $caso = 1;
            }
            if ($valorEnvio > $pagoTotal) {
                $caso = 2;
            }
            if ($valorEnvio < $pagoTotal) {
                $caso = 3;
            }
            $valorWhat = $valorEnvio;
            $valorTotal = BaseController::valorTotal();
            $detalle = CreditFolderDetail::find($this->idDeLetra);

            $formapagoData = $this->formasPagarCredito[0] ?? null;

            $bancoAsignado = $formapagoData['banco_pago_id'] ?? null;
            $comprobanteAsignado =
                ($formapagoData && $formapagoData['formaName'] == 'TRANSFERENCIA')
                ? ($formapagoData['numero_comprobante'] ?? null)
                : null;


            switch ($caso) {
                case 1:
                    $detalle = CreditFolderDetail::find($this->idDeLetra);
                    $detalle->valor_pagado = $valorEnvio;
                    $detalle->valor_final = $valorEnvio;
                    $detalle->tipo_pago = $this->formaPago;
                    $detalle->banco_id = ($bancoAsignado != '' and $bancoAsignado != null) ?  $bancoAsignado : null;
                    //$detalle->numero_comprobante = $comprobanteAsignado;
                    $detalle->obervation_pago = ($this->observacionPagoLetra != null) ? $this->observacionPagoLetra : null;
                    $detalle->date_pay = date('Y-m-d');
                    $detalle->hour_pay = date('H:i:s');
                    $detalle->user_pay_id = Auth::user()->id;
                    $detalle->status = 'PAGADA';
                    $detalle->interes_mora = $this->interesMora;
                    $detalle->save();
                    $this->manejarTransicionEstado($detalle);
                    $customer = Customer::find($this->customerID);

                    foreach ($this->formasPagarCredito as $formasPago) {

                        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                            ->where('name_corto', 'PC')
                            ->first();
                        $tabla = 'customer_movimientos';
                        $code = BaseController::generarCodigo($tabla, 9);
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
                            "valor_movimiento" => $formasPago['valor'],
                            "saldo_general" => $valorTotal + $formasPago['valor'],
                            "credit_folder_details_id" => $this->idDeLetra,
                            "observation" => 'PAGO DEL PRESTAMO ' . $detalle->code_folder_header . ', LA LETRA ' . $detalle->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos,
                            "user_created_id" => Auth::user()->id,
                            "date_created" => $this->fechaPagoLetra,
                            "hour_created" => date("H:i:s"),
                            "forma_pago_id" => $formasPago['formaId'],
                            "forma_pago_name" => $formasPago['formaName'],
                            "banco_id" => strtoupper(trim($formasPago['formaName'])) == 'TRANSFERENCIA' ? ($formasPago['banco_pago_id'] ?? null) : null,

                        ];
                        $movimientos = CustomerMovimiento::create($data);
                        AsientosHeader::recalcularAsientos($movimientos->id, 'customer_movimientos');
                        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorEnvio, $customer->code, $movimientos->saldo_general, $this->fechaPagoLetra);
                    }

                    break;
                case 2:
                    $detalle = CreditFolderDetail::find($this->idDeLetra);
                    $SOBRANTE = (float) round(($valorEnvio - $detalle->valor_cuota - $this->interesMora - $detalle->faltante_anterior_cuota + $detalle->saldo_anterior_cuota), 2);
                    $detalle->valor_pagado = $valorEnvio;
                    $detalle->valor_final = $valorEnvio;
                    $detalle->tipo_pago = $this->formaPago;
                    $detalle->banco_id = ($bancoAsignado != '' and $bancoAsignado != null) ?  $bancoAsignado : null;
                    //$detalle->numero_comprobante = $comprobanteAsignado;
                    $detalle->adelanto_prox_cuota = $SOBRANTE;
                    $detalle->obervation_pago = ($this->observacionPagoLetra !== null) ? $this->observacionPagoLetra : null;
                    $detalle->date_pay = date('Y-m-d');
                    $detalle->hour_pay = date('H:i:s');
                    $detalle->user_pay_id = Auth::user()->id;
                    $detalle->status = 'PAGADA';
                    $detalle->interes_mora = $this->interesMora;
                    $detalle->save();
                    $this->manejarTransicionEstado($detalle);
                    while ($SOBRANTE > 0) {
                        $letrasImpagas = CreditFolderDetail::where('code_folder_header', $detalle->code_folder_header)
                            ->where('status', 'PENDIENTE')
                            ->where('saldo_anterior_cuota', '0.00')
                            ->orderBy('numero_cuota')
                            ->first();
                        $saldo = $this->adelantarLetra($letrasImpagas->id, $SOBRANTE);
                        $SOBRANTE = (float) round($saldo, 2);
                    }
                    $customer = Customer::find($this->customerID);

                    foreach ($this->formasPagarCredito as $formasPago) {

                        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                            ->where('name_corto', 'PC')
                            ->first();
                        $tabla = 'customer_movimientos';
                        $code = BaseController::generarCodigo($tabla, 9);
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
                            "valor_movimiento" => $formasPago['valor'],
                            "saldo_general" => $valorTotal + $formasPago['valor'],
                            "credit_folder_details_id" => $this->idDeLetra,
                            "observation" => 'PAGO DEL PRESTAMO ' . $detalle->code_folder_header . ', LA LETRA ' . $detalle->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos . '; CON ABONO A LA SIGUIENTE LETRA',
                            "user_created_id" => Auth::user()->id,
                            "date_created" => $this->fechaPagoLetra,
                            "hour_created" => date("H:i:s"),
                            "forma_pago_id" => $formasPago['formaId'],
                            "forma_pago_name" => $formasPago['formaName'],
                            "banco_id" => strtoupper(trim($formasPago['formaName'])) == 'TRANSFERENCIA' ? ($formasPago['banco_pago_id'] ?? null) : null,
                        ];

                        $movimientos = CustomerMovimiento::create($data);
                        AsientosHeader::recalcularAsientos($movimientos->id, 'customer_movimientos');
                        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorEnvio, $customer->code, $movimientos->saldo_general, $this->fechaPagoLetra);
                    }
                    break;
                case 3:
                    $detalle = CreditFolderDetail::find($this->idDeLetra);
                    $FALTANTE = (float) round($detalle->valor_cuota + $this->interesMora + $detalle->faltante_anterior_cuota - $detalle->saldo_anterior_cuota - $valorEnvio, 2);
                    $this->verificarPagoMenorUltimaLetra($detalle, $FALTANTE);
                    $detalle->valor_pagado = $valorEnvio;
                    $detalle->valor_final = $valorEnvio;
                    $detalle->tipo_pago = $this->formaPago;
                    $detalle->banco_id = ($bancoAsignado != '' and $bancoAsignado != null) ?  $bancoAsignado : null;
                    //$detalle->numero_comprobante = $comprobanteAsignado;
                    $detalle->faltante_prox_cuota = $FALTANTE;
                    $detalle->obervation_pago = ($this->observacionPagoLetra !== null) ? $this->observacionPagoLetra : null;
                    $detalle->date_pay = date('Y-m-d');
                    $detalle->hour_pay = date('H:i:s');
                    $detalle->user_pay_id = Auth::user()->id;
                    $detalle->status = 'PAGADA';
                    $detalle->interes_mora = $this->interesMora;
                    $detalle->save();
                    $this->manejarTransicionEstado($detalle);
                    $letrasImpagas = CreditFolderDetail::where('code_folder_header', $detalle->code_folder_header)
                        ->where('status', 'PENDIENTE')
                        ->where('faltante_anterior_cuota', '0.00')
                        ->orderBy('numero_cuota')
                        ->first();
                    $this->faltanteLetra($letrasImpagas->id, $FALTANTE);
                    $customer = Customer::find($this->customerID);

                    foreach ($this->formasPagarCredito as $formasPago) {
                        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                            ->where('name_corto', 'PC')
                            ->first();
                        $tabla = 'customer_movimientos';
                        $code = BaseController::generarCodigo($tabla, 9);
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
                            "valor_movimiento" => $formasPago['valor'],
                            "saldo_general" => $valorTotal + $formasPago['valor'],
                            "credit_folder_details_id" => $this->idDeLetra,
                            "observation" => 'PAGO DEL PRESTAMO ' . $detalle->code_folder_header . ', LA LETRA ' . $detalle->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos . '; CON UN VALOR FALTANTE QUE SE SUMARÁ A LA SIGUIENTE LETRA',
                            "user_created_id" => Auth::user()->id,
                            "date_created" => $this->fechaPagoLetra,
                            "hour_created" => date("H:i:s"),
                            "forma_pago_id" => $formasPago['formaId'],
                            "forma_pago_name" => $formasPago['formaName'],
                            "banco_id" => strtoupper(trim($formasPago['formaName'])) == 'TRANSFERENCIA' ? ($formasPago['banco_pago_id'] ?? null) : null,
                        ];

                        $movimientos = CustomerMovimiento::create($data);                    //                CajasController::calcularCajaAutomatica();
                        AsientosHeader::recalcularAsientos($movimientos->id, 'customer_movimientos');
                        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorEnvio, $customer->code, $movimientos->saldo_general, $this->fechaPagoLetra);
                    }
                    break;
            }
            $this->generarGastoCobranza($this->gasto_cobranza, $this->customerID, $this->idDeLetra, ' DEL CREDITO ' . $detalle->code_folder_header . ' DE LA LETRA' . $detalle->code_folder_header);
            $cabecera = CreditFolderHeader::where('code', $cuota->code_folder_header)->first();
            $valornew = $cabecera->total_pagando + $valorEnvio;
            $cabecera->total_pagando = $valornew;
            $cabecera->save();

            $this->guardarFormasPago($this->formasPagarCredito, $movimientos, $this->idDeLetra);
            $this->contabilizarPagosDirectosLetra($this->idDeLetra);

            $this->formaPago = '';
            $this->interesMora = '';
            $this->valorPagarLetra = '';
            $this->observacionPagoLetra = '';
            $this->cargarDatosPrestamo($this->headerPago);
            $this->seleccionarCliente($this->id_seleccionado);
            $this->enviarMensajePago($this->idDeLetra, $valorWhat, $cuota->code_folder_header, $movimientos);
            $this->descLetra = '';
            $this->formasPagarCredito = [];
        }
    }

    private function manejarTransicionEstado(CreditFolderDetail $detalle): void
    {
        $header = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        if (!$header) {
            return;
        }
        // LÓGICA DE TRANSICIÓN ENPRUEBA -> APROBADO
        $prestamo = Prestamos::find($header->tipo_prestamo);
        if ($header->status === 'ENPRUEBA' && $prestamo && !$prestamo->lleva_contabilidad) {
            $cuotas_pagadas = CreditFolderDetail::where('code_folder_header', $header->code)
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

    public function depositarAhorro($id)
    {
        $sejecucion = true;
        $cuota = CreditFolderDetail::find($id);
        $customerTipoAhorro = CustomerTipoAhorros::join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
            ->where('tipo_ahorros.ahorro_prestamo', true)
            ->where('customer_tipo_ahorros.customer_id', $this->id_seleccionado)
            ->select('customer_tipo_ahorros.id as idCuentaCliente', 'customer_tipo_ahorros.codigo')
            ->first();
        if (isset($customerTipoAhorro->idCuentaCliente)) {
            $customer = Customer::find($this->id_seleccionado);
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'IN')
                ->first();
            $tabla = 'customer_movimientos';
            $code = BaseController::generarCodigo($tabla, 9);
            $valorTotal = BaseController::valorTotal();

            $data = [
                "code" => $code,
                "comprobante" => $code . '-' . date('YmdHmi'),
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "afecta" => $transaction->afecta,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_ruc" => $customer->numero_documento,
                "customer_address" => $customer->direccion,
                "customer_telefono" => $customer->telefono,
                "customer_tipo_ahorro_id" => $customerTipoAhorro->idCuentaCliente,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor_movimiento" => $this->valor_ahorro,
                "saldo_general" => $valorTotal + $this->valor_ahorro,
                "observation" => 'Ahorro de credito: ' . $cuota->code_folder_header . ' de la letra: ' . $cuota->numero_cuota,
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),

            ];

            $movimientos = CustomerMovimiento::create($data);
            CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $this->valor_ahorro, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
            BaseController::guardarValoresCartola($customerTipoAhorro->idCuentaCliente, $customer->id, $movimientos, $transaction);
            BaseController::enviarMail($movimientos);
        } else {
            $sejecucion = false;
        }
        return $sejecucion;
    }

    public function verificarPagoMenorUltimaLetra($detalle, $FALTANTE)
    {
        $cantidadPendiente = CreditFolderDetail::where('code_folder_header', $detalle->code_folder_header)->where('status', 'PENDIENTE')->count();
        if ($cantidadPendiente == 1) {
            $nuevaLetra = new CreditFolderDetail();
            $nuevaLetra->numero_cuota = $detalle->numero_cuota + 1;
            $nuevaLetra->company_id = Auth::user()->company_id;
            $nuevaLetra->code_folder_header = $detalle->code_folder_header;

            $fecha = $detalle->date_vencimiento;
            $fechaDateTime = new DateTime($fecha);
            $fechaDateTime->modify('+1 month');
            $fechaFinal = $fechaDateTime->format('Y-m-d');
            $nuevaLetra->date_vencimiento = $fechaFinal;
            $nuevaLetra->status = 'PENDIENTE';
            $nuevaLetra->save();
        }
    }

    public function generarGastoCobranza($valor, $cliente, $letra, $texto)
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->genera_gastos_cobranza) {
            if ($valor != '' && $valor > 0) {
                $customer = Customer::find($cliente);
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'GGC')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
                $valorTotal = BaseController::valorTotal();
                $detalle = CreditFolderDetail::find($letra);
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
                    "valor_movimiento" => $valor,
                    "saldo_general" => $valorTotal + $valor,
                    "observation" => $transaction->description . $texto,
                    "user_created_id" => Auth::user()->id,
                    "date_created" => $this->fechaPagoLetra,
                    "hour_created" => date("H:i:s"),
                ];
                $movimientos = CustomerMovimiento::create($data);
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, $this->fechaPagoLetra);
            }
        }
    }

    public function guardarFormasPago($formas, $movimiento, $letra)
    {
        foreach ($formas as $formasPago) {
            $data = [
                'company_id' => Auth::user()->company_id,
                'customer_movimientos_id' => $movimiento->id,
                'customer_id' => $movimiento->customer_id,
                'letra_id' => $letra,
                'forma_pago' => $formasPago['formaName'],
                'forma_pago_id' => $formasPago['formaId'],
                'valor' => $formasPago['valor'],
                'date_create' => date('Y-m-d'),
                'hour_create' => date('H:i:s'),
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->username,
                'banco_id' => ($formasPago['formaName'] == 'TRANSFERENCIA') ? $formasPago['banco_pago_id'] : null,
                'status' => 1,
            ];
            RegistroFormasPago::create($data);
        }
    }

    public function guardarFormasPagoPendientes($formas, $letra, $movimiento = null)
    {

        //$valorMora = BaseController::calculoInteresMoraLetraValorMensual($letra);
        $valorMora = $this->interesMora;

        $letraCredito = CreditFolderDetail::find($letra);
        $letraCredito->status = 'STAND BY';
        $letraCredito->interes_mora = $valorMora;
        $letraCredito->save();

        $cabecera = CreditFolderHeader::where('code', $letraCredito->code_folder_header)->first();


        foreach ($formas as $formasPago) {
            $data = [
                'company_id' => Auth::user()->company_id,
                'customer_movimientos_id' => ($movimiento != null) ? $movimiento->id : null,
                'customer_id' => $cabecera->customer_id,
                'letra_id' => $letra,
                'forma_pago' => $formasPago['formaName'],
                'forma_pago_id' => $formasPago['formaId'],
                'valor' => $formasPago['valor'],
                'date_create' => date('Y-m-d'),
                'hour_create' => date('H:i:s'),
                'user_id' => Auth::user()->id,
                'user_name' => Auth::user()->username,
                'banco_id' => ($formasPago['formaName'] == 'TRANSFERENCIA') ? $formasPago['banco_pago_id'] : null,
                'numero_comprobante' => ($formasPago['formaName'] == 'TRANSFERENCIA')
                    ? ($formasPago['numero_comprobante'] ?? null)
                    : null,
                'status' => 3,
                'solicitado' => 3,
                'usuario_solicitud' => null,
                'usuario_id_solicitud' => null,
                'fecha_solicitud' => null,
                'hora_solicitud' => null,
            ];
            RegistroFormasPago::create($data);
        }
        $this->formasPagarCredito = [];
    }


    public function enviarMensajePago($id, $valor, $code, $movimientos)
    {
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PC')
            ->where('action', 'S')
            ->where('afecta', 'C')
            ->first();
        $cabecera = CreditFolderHeader::where('code', $code)->first();
        $cuota = CreditFolderDetail::find($id);
        if ($cabecera->customer_id != null) {
            $customer = Customer::find($cabecera->customer_id);
        } else {
            $customer = Customer::where('code', $cabecera->customer_code)->first();
        }
        $whatsappEnviar = 'Estimad@, *' . $cabecera->customer_name . '* se pagó la letra ' . $cuota->numero_cuota . ' del del crédito : *' . $cabecera->code . '* total pagado ' . $valor . '$ Gracias por usar nuestros servicios de .' . Auth::user()->company->comercial_name;
        $whatsapp = new WhaEnvios();
        $whatsapp->company_id = Auth::user()->company_id;
        $whatsapp->envio_ahora = true;
        $whatsapp->es_transacion = true;
        $whatsapp->inmediato = true;
        $whatsapp->description = 'ENVIO DE WHATSAPP DESDE CREDITOS';
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
        $whatsapp->prestamo_id = $cabecera->id;
        $whatsapp->letra_prestamo_id = $id;
        $whatsapp->save();
    }

    public function envioWhatsapp($id)
    {
        $celular = '';
        $mensaje = '';
        $detalle = CreditFolderDetail::find($id);
        $cabecera = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        if (!empty($cabecera->customer_id)) {
            $customer = Customer::find($cabecera->customer_id);
            $celular = '593' . $customer->telefono;

            $country = Country::find($customer->country_id);
            if ($country->codigo_pais == '593') {
                $celular = '593' . $customer->telefono;
            } else {
                $celular = $country->codigo_pais . $customer->telefono;
            }





            $mensaje = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* se pagó la letra ' . $detalle->numero_cuota . ' del del crédito : *' . $cabecera->code . '* total pagado ' . $detalle->valor_pagado . '$ Gracias por usar nuestros servicios de ' . Auth::user()->company->comercial_name;
            $data = [
                'code' => '200',
                'telefono' => $celular,
                'mensaje' => $mensaje,
            ];
            $this->dispatchBrowserEvent('whatsapp', $data);
        }
    }

    public function notificar($id, $cantidad)
    {
        $letra = CreditFolderDetail::find($id);
        $letra->notificado = $cantidad + 1;
        $letra->save();
        $color = 'info';
        $mensaje = 'Notificacion Generada correctamente!';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->cargarDatosPrestamo($this->headerPago);
        $this->dispatchBrowserEvent('alerta', $data);
        $this->consultarDatosLetra($id);
    }

    public function reiniciarVista()
    {
        if (!empty($this->idDeLetra)) {
            $this->contabilizarPagosDirectosLetra($this->idDeLetra);
        }

        $this->seleccionarCliente($this->id_seleccionado);
    }

    private function contabilizarPagosDirectosLetra($letraId): void
    {
        $tieneTransferenciaPendiente = RegistroFormasPago::where('letra_id', $letraId)
            ->where('forma_pago', 'TRANSFERENCIA')
            ->where('solicitado', 3)
            ->exists();

        if ($tieneTransferenciaPendiente) {
            return;
        }

        $movimientos = CustomerMovimiento::where('credit_folder_details_id', $letraId)->get();
        foreach ($movimientos as $movimiento) {
            AsientosHeader::recalcularAsientos($movimiento->id, 'customer_movimientos');
        }
    }

    public function aprobarCredito($id)
    {
        $credito = CreditFolderHeader::find($id);
        $prestamo = Prestamos::find($credito->tipo_prestamo);
        $llevaContabilidad = (bool) $prestamo->lleva_contabilidad;
        $credito->date_verified = date('Y-m-d');
        $credito->hour_verified = date("H:i:s");
        $credito->user_verified_id = Auth::user()->id;
        $credito->user_verified_name = Auth::user()->username;
        if (!$llevaContabilidad) {
            $credito->status = 'ENPRUEBA';
            $mensaje = 'Crédito aprobado. Requiere pagos de prueba.';
            $color = 'primary';
        } else {
            $credito->status = 'APROBADO';
            $mensaje = 'Crédito aprobado. Listo para desembolso.';
            $color = 'success';
        }
        $credito->save();
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function entregarDineroModal($id)
    {
        $this->cuentasDes = '';
        $this->formaPagoEntregaCredito = '';
        $credito = CreditFolderHeader::find($id);
        $customer = Customer::find($credito->customer_id);
        $cuenta = CustomerTipoAhorros::join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', 'tipo_ahorros.id')
            ->where('tipo_ahorros.descargo_creditos', true)
            ->where('customer_tipo_ahorros.company_id', Auth::user()->company_id)
            ->where('customer_tipo_ahorros.customer_id', $customer->id)
            ->select('customer_tipo_ahorros.codigo', 'customer_tipo_ahorros.id', 'tipo_ahorros.name')
            ->get();
        $this->cuentasDes = $cuenta;
        $this->creditoDineroEntregar = $id;
    }
    public function entregarDinero()
    {
        /**
         * [MODIFICACION - CONTABILIDAD]
         * Se ha implementado la generación automática de asientos contables.
         * Flujo:
         * 1. Inicio de Transacción (DB::beginTransaction).
         * 2. Obtención dinámica de cuentas:
         *    - DEBE: Del Tipo de Préstamo (plan_cuenta_id).
         *    - HABER: Del Banco seleccionado (PlanCuentas asociado).
         * 3. Validación: Si faltan cuentas, se revierte y muestra error.
         * 4. Creación de AsientosHeader y AsientosDetalle.
         */

        $formaPago = FormasPago::find($this->formaPagoEntregaCredito);
        $cuentaAcreditar = 0;
        if ($formaPago) {
            $cuentaAcreditar = $formaPago->credito_descargo_boveda;
        }
        $esEfectivo = (int) $cuentaAcreditar === 1;
        //dd($cuentaAcreditar,  $formaPago);
        $this->validate(
            [
                'formaPagoEntregaCredito' => 'required',
                'cuenta_credito' => ($this->formaPagoEntregaCredito == 0 || $cuentaAcreditar == 0) ? 'required' : '',
                //'cuenta_credito' => 'required',
                'documento_desembolso' => ($this->obligarCuentaInterna == 0) ? 'required' : '',
                'banco_id' => ($this->obligarCuentaInterna == 0 && !$esEfectivo) ? 'required' : '',
            ],
            [
                'formaPagoEntregaCredito.required' => 'Debe Seleecionar una forma de acreditación.',
                'cuenta_credito.required' => 'Debe Seleecionar una cuenta a la que quiera acreditar el valor del prestamo.',
                'documento_desembolso.required' => 'Debe ingresar un numero de documento (Transacción).',
                'banco_id.required' => 'Debe seleccionar un banco.',
            ]
        );

        $id = $this->creditoDineroEntregar;
        if ($cuentaAcreditar == 0) {
            $respuesta = $this->verficarExisteCuentaDescargos($id, $this->cuenta_credito);
            if (!$respuesta) {
                $color = 'danger';
                $mensaje = 'No se puede aprobar el crédito, porque no existe una cuenta de descargos';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                $this->generarListadeCreditos();
                return;
            }
        }
        $credito = CreditFolderHeader::find($id);
        if (!$credito) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'No existe el credito seleccionado para desembolso.'
            ]);
            return;
        }

        $prestamo = Prestamos::find($credito->tipo_prestamo);
        if (!$prestamo) {
            $this->dispatchBrowserEvent('alerta', [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => 'El credito no tiene un tipo de prestamo valido configurado.'
            ]);
            return;
        }

        $llevaContabilidad = (bool) $prestamo->lleva_contabilidad;
        $cuentaContableHaber = 0;

        if ($esEfectivo) {
            // Efectivo: el HABER contable sale de Caja General.
            $cuentaContableHaber = null;
        } else {
            // Desembolso por Caja/Banco (HABER = Cuenta seleccionada en $this->banco_id)
            $cuentaContableHaber = $this->banco_id;
        }

        DB::beginTransaction();
        try {
            // --- INICIO LOGICA CONTABLE ---
            $creditoHeader = $credito; // Reuse found record
            $tipoPrestamo = $prestamo; // Reuse found record

            // 1. Obtener Cuenta DEBE (Cartera)
            $cuentaDebeId = $creditoHeader->cuenta_contable_id;
            if (!$cuentaDebeId) {
                $cuentaMadre = PlanCuentas::find($tipoPrestamo->plan_cuenta_id);
                if ($cuentaMadre) {
                    $plazoMeses = $creditoHeader->cuotas_pagar;
                    $plazoEnDias = $plazoMeses * 30;
                    $codigoMadre = $cuentaMadre->codigo;
                    $codigoHija = '';

                    if ($plazoEnDias <= 30) {
                        $codigoHija = $codigoMadre . '.05';
                    } elseif ($plazoEnDias <= 90) {
                        $codigoHija = $codigoMadre . '.10';
                    } elseif ($plazoEnDias <= 180) {
                        $codigoHija = $codigoMadre . '.15';
                    } elseif ($plazoEnDias <= 360) {
                        $codigoHija = $codigoMadre . '.20';
                    } else {
                        $codigoHija = $codigoMadre . '.25';
                    }

                    $cuentaHija = PlanCuentas::where('codigo', $codigoHija)->where('company_id', Auth::user()->company_id)->first();
                    if ($cuentaHija) {
                        $cuentaDebeId = $cuentaHija->id;
                    }
                }
            }
            if (!$cuentaDebeId) {
                $cuentaDebeId = $tipoPrestamo->plan_cuenta_id;
            }

            // 2. Obtener Cuenta HABER (Caja/Banco)
            $cuentaHaberId = null;

            if ($esEfectivo) {
                $cuentaCaja = PlanCuentas::where('company_id', Auth::user()->company_id)
                    ->where(function ($query) {
                        $query->where('codigo', '1.1.01.05.01')
                            ->orWhere('nombre', 'like', '%CAJA GENERAL%');
                    })
                    ->where('status', true)
                    ->orderBy('codigo')
                    ->first();

                if ($cuentaCaja) {
                    $cuentaHaberId = $cuentaCaja->id;
                }
            } else {

                if (!$cuentaHaberId && $this->banco_id) {
                    $cuentaBanco = PlanCuentas::where('banco_id', $this->banco_id)
                        ->where('company_id', Auth::user()->company_id)
                        ->where('codigo', 'like', '1.1.%')
                        ->where('status', true)
                        ->orderBy('codigo')
                        ->first();

                    if ($cuentaBanco) {
                        $cuentaHaberId = $cuentaBanco->id;
                    }
                }
            }

            if (!$cuentaDebeId) {
                $this->addError('cuenta_debe', 'El Tipo de Préstamo no tiene configurada una Cuenta Contable (Cartera).');
                DB::rollBack();
                return;
            }


            if (!$cuentaHaberId) {
                $debugInfo = "FormaPago: " . ($formaPago->nombre ?? 'NULO') . " - BancoID: " . ($this->banco_id ?? 'NULO') . " - Empresa: " . Auth::user()->company_id;
                $this->addError('cuenta_haber', 'No se encontró cuenta contable para el Banco/Caja/Ahorro seleccionado. ' . $debugInfo);
                DB::rollBack();
                return;
            }

            // 1. Generar Movimientos
            $movimiento = null;
            //dd($movimiento, $esEfectivo, $id, $llevaContabilidad, $cuentaContableHaber);
            if ($esEfectivo) {
                $movimiento = $this->movimientoCreditoCuentaEmpresa($id, $llevaContabilidad, $cuentaContableHaber);
                $this->movimientoCreditoCuentaCliente($id, $this->cuenta_credito);
            } else {
                $this->descargarBalorBoveda($id);
                $movimiento = $this->movimientoCreditoCuentaEmpresa($id, $llevaContabilidad, $cuentaContableHaber);
            }

            $credito->date_verified = date('Y-m-d');
            $credito->hour_verified = date("H:i:s");
            $credito->user_verified_id = Auth::user()->id;
            $credito->user_verified_name = Auth::user()->username;
            $credito->status = 'ENTREGADO';
            $credito->forma_pago_id = $this->formaPagoEntregaCredito;
            $credito->documento_desembolso = $this->documento_desembolso;
            $credito->save();

            $this->contabilizarMovimientoSiDiferido($credito->id, $llevaContabilidad);
            $this->seleccionarCliente($this->id_seleccionado);
            $this->dispatchBrowserEvent('closeModal');


            // 2. Lógica Contable (Vinculada al Movimiento)
            $company = Company::find(Auth::user()->company_id);
            if ($movimiento && $company && $company->puedeContabilizar($movimiento->date_created)) {
                $valorDesembolsoContable = $movimiento->valor_movimiento;
                // Buscar el AsientoHeader creado automáticamente por el Observer
                $asiento = AsientosHeader::where('customer_movimiento_id', $movimiento->id)->first();

                // Si no existe (por falta de config), lo creamos manualmente vinculado
                if (!$asiento) {
                    $asiento = AsientosHeader::create([
                        'company_id' => Auth::user()->company_id,
                        'manual' => false,
                        'fecha_contable' => date('Y-m-d H:i:s'),
                        'fecha_creacion' => date('Y-m-d H:i:s'),
                        'user_created' => Auth::user()->id,
                        'concepto_id' => 2, // EGRESO
                        'descripcion' => 'ENTREGA DE CRÉDITO N° ' . $creditoHeader->code . ' - SOCIO: ' . $creditoHeader->customer_name,
                        'status' => true,
                        'glosa' => 'ENTREGA DE CRÉDITO N° ' . $creditoHeader->code,
                        'customer_movimiento_id' => $movimiento->id
                    ]);
                } else {
                    // Si existe, nos aseguramos que tenga el concepto correcto y limpiamos detalles basura
                    $asiento->concepto_id = 2; // EGRESO
                    $asiento->status = true;
                    $asiento->descripcion = 'ENTREGA DE CRÉDITO N° ' . $creditoHeader->code . ' - SOCIO: ' . $creditoHeader->customer_name;
                    $asiento->save();
                    // Borrar detalles vacíos o incorrectos que haya creado el observer
                    AsientosDetalle::where('asientos_header_id', $asiento->id)->delete();
                }

                // Detalle DEBE (Cartera)
                AsientosDetalle::create([
                    'company_id' => Auth::user()->company_id,
                    'asientos_header_id' => $asiento->id,
                    'plan_cuentas_id' => $cuentaDebeId,
                    'valor' => $valorDesembolsoContable,
                    'debe_haber' => 1, // Debe (1=DEBE)
                    'fecha_contable' => date('Y-m-d H:i:s'),
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'user_created' => Auth::user()->id,
                    'observacion' => 'CARTERA DE CRÉDITO',
                    'status' => true
                ]);

                // Detalle HABER (Banco/Caja)
                AsientosDetalle::create([
                    'company_id' => Auth::user()->company_id,
                    'asientos_header_id' => $asiento->id,
                    'plan_cuentas_id' => $cuentaHaberId,
                    'valor' => $valorDesembolsoContable,
                    'debe_haber' => 0, // Haber (0=HABER)
                    'fecha_contable' => date('Y-m-d H:i:s'),
                    'fecha_creacion' => date('Y-m-d H:i:s'),
                    'user_created' => Auth::user()->id,
                    'observacion' => 'DESEMBOLSO DE CRÉDITO',
                    'status' => true
                ]);
            }

            $this->contabilizarInteresesGeneradosCredito(
                $creditoHeader,
                $tipoPrestamo,
                $company,
                $movimiento ? $movimiento->date_created : date('Y-m-d')
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $data = [
                'titulo' => 'Error',
                'color' => 'danger',
                'mensaje' => $e->getMessage()
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
        $this->dispatchBrowserEvent('closeModal');
    }

    private function contabilizarInteresesGeneradosCredito($creditoHeader, $tipoPrestamo, $company, $fechaContable): void
    {
        $valorInteres = (float) round(CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $creditoHeader->code)
            ->sum('interes_periodo'), 2);

        if ($valorInteres <= 0 || !$company || !$company->puedeContabilizar($fechaContable)) {
            return;
        }

        $configHeader = $this->buscarConfigInteresesGenerados();
        if (!$configHeader) {
            throw new \Exception('No existe configuración contable DIARIO para INTERESES GENERADOS.');
        }

        $configDetalle = ConfigPlanDetalle::with('planCuentas')
            ->where('company_id', Auth::user()->company_id)
            ->where('config_plan_header_id', $configHeader->id)
            ->where('status', true)
            ->orderBy('debe_haber', 'DESC')
            ->get()
            ->filter(function ($detalle) use ($creditoHeader, $tipoPrestamo) {
                return $this->cuentaInteresAplicaACredito($detalle->planCuentas, $creditoHeader, $tipoPrestamo);
            })
            ->values();

        if ($configDetalle->isEmpty()) {
            throw new \Exception('La configuración de INTERESES GENERADOS no tiene cuentas aplicables al tipo de crÃ©dito.');
        }

        $descripcion = 'INTERESES GENERADOS CREDITO N ' . $creditoHeader->code . ' - SOCIO: ' . $creditoHeader->customer_name;

        $asiento = AsientosHeader::where('company_id', Auth::user()->company_id)
            ->where('config_plan_header_id', $configHeader->id)
            ->where('descripcion', $descripcion)
            ->where('status', true)
            ->first();

        if (!$asiento) {
            $asiento = new AsientosHeader();
            $asiento->company_id = Auth::user()->company_id;
        }

        $fechaHoraContable = date('Y-m-d H:i:s', strtotime($fechaContable ?: date('Y-m-d')));

        $asiento->manual = false;
        $asiento->fecha_contable = $fechaHoraContable;
        $asiento->fecha_creacion = date('Y-m-d H:i:s');
        $asiento->user_created = Auth::user()->id;
        $asiento->concepto_id = $configHeader->concepto_id;
        $asiento->config_plan_header_id = $configHeader->id;
        $asiento->descripcion = $descripcion;
        $asiento->status = true;
        $asiento->save();

        AsientosDetalle::where('company_id', Auth::user()->company_id)
            ->where('asientos_header_id', $asiento->id)
            ->delete();

        foreach ($configDetalle as $detalle) {
            AsientosDetalle::create([
                'company_id' => Auth::user()->company_id,
                'asientos_header_id' => $asiento->id,
                'plan_cuentas_id' => $detalle->plan_cuentas_id,
                'valor' => $valorInteres,
                'debe_haber' => $detalle->debe_haber,
                'fecha_contable' => $fechaHoraContable,
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'user_created' => Auth::user()->id,
                'observacion' => 'INTERESES GENERADOS DEL CREDITO',
                'status' => true
            ]);
        }

        if (!AsientosHeader::estaCuadrado($asiento->id)) {
            $totales = AsientosHeader::totales($asiento->id);
            AsientosDetalle::where('company_id', Auth::user()->company_id)
                ->where('asientos_header_id', $asiento->id)
                ->delete();
            $asiento->delete();

            throw new \Exception('El asiento de intereses generados queda descuadrado. Debe: '
                . number_format($totales['debe'], 2, '.', '')
                . ' Haber: '
                . number_format($totales['haber'], 2, '.', '')
                . '. Revise la configuración INTERESES GENERADOS.');
        }
    }

    private function buscarConfigInteresesGenerados()
    {
        $base = ConfigPlanHeader::select('config_plan_header.*')
            ->join('type_transactions', 'type_transactions.id', '=', 'config_plan_header.type_transaction_id')
            ->join('conceptos', 'conceptos.id', '=', 'config_plan_header.concepto_id')
            ->where('config_plan_header.company_id', Auth::user()->company_id)
            ->where('config_plan_header.status', true)
            ->whereRaw('UPPER(conceptos.nombre) = ?', ['DIARIO']);

        $configInteres = (clone $base)
            ->where(function ($query) {
                $query->whereRaw('UPPER(type_transactions.name) LIKE ?', ['%INTERES%'])
                    ->whereRaw('UPPER(type_transactions.name) LIKE ?', ['%GENERAD%']);
            })
            ->first();

        if ($configInteres) {
            return $configInteres;
        }

        return (clone $base)
            ->where('type_transactions.name_corto', 'ENC')
            ->first();
    }

    private function cuentaInteresAplicaACredito($planCuenta, $creditoHeader, $tipoPrestamo): bool
    {
        if (!$planCuenta) {
            return false;
        }

        $codigo = (string) $planCuenta->codigo;
        $esMicrocredito = $this->creditoEsMicrocredito($creditoHeader, $tipoPrestamo);

        if (strpos($codigo, '2.9.03.') === 0) {
            return $esMicrocredito;
        }

        if (strpos($codigo, '1.6.03.') === 0) {
            return !$esMicrocredito;
        }

        if (strpos($codigo, '5.1.04.') !== 0) {
            return true;
        }

        if (strpos($codigo, '.20') !== false) {
            return $esMicrocredito;
        }

        if (strpos($codigo, '.10') !== false) {
            return !$esMicrocredito;
        }

        return true;
    }

    private function creditoEsMicrocredito($creditoHeader, $tipoPrestamo): bool
    {
        $texto = strtoupper(
            trim((string) ($tipoPrestamo->name ?? '') . ' ' . (string) ($tipoPrestamo->tipo ?? ''))
        );

        if (strpos($texto, 'MICRO') !== false) {
            return true;
        }

        $cuentaCredito = PlanCuentas::find($creditoHeader->cuenta_contable_id ?: $tipoPrestamo->plan_cuenta_id);
        $textoCuenta = strtoupper(trim((string) ($cuentaCredito->nombre ?? '') . ' ' . (string) ($cuentaCredito->codigo ?? '')));

        return strpos($textoCuenta, 'MICRO') !== false
            || strpos($textoCuenta, '1.4.04') === 0
            || strpos($textoCuenta, '1.4.12') === 0
            || strpos($textoCuenta, '1.4.20') === 0
            || strpos($textoCuenta, '1.4.28') === 0
            || strpos($textoCuenta, '1.4.36') === 0
            || strpos($textoCuenta, '1.4.44') === 0
            || strpos($textoCuenta, '1.4.52') === 0
            || strpos($textoCuenta, '1.4.60') === 0
            || strpos($textoCuenta, '1.4.68') === 0;
    }

    public function contabilizarMovimientoSiDiferido($credito_id, $lleva_contabilidad)
    {
        if (!$lleva_contabilidad) {
            $movimientos_pendientes = CustomerMovimiento::where('credit_folder_header_id', $credito_id)
                ->where('automatico', false)
                ->get();
            if ($movimientos_pendientes->count() > 0) {
                foreach ($movimientos_pendientes as $movimiento) {
                    AsientosHeader::recalcularAsientos($movimiento->id, 'customer_movimientos');
                    $movimiento->automatico = true;
                    $movimiento->save();
                }
                $this->dispatchBrowserEvent('alerta', [
                    'titulo' => 'Contabilidad Diferida Completada',
                    'color' => 'success',
                    'mensaje' => 'Se han contabilizado exitosamente ' . $movimientos_pendientes->count() . ' movimientos pendientes de este crédito.'
                ]);
            }
        }
    }

    public function editarCredito($id)
    {
        $credito = CreditFolderHeader::find($id);
        $prestamo = Prestamos::find($credito->tipo_prestamo);
        $prestamosList = Prestamos::where('tipo', $prestamo->tipo)
            ->where('periodo_id', '!=', null)
            ->where('status', 'A')
            ->get();
        $this->opcionesPrestamoEditar = $prestamosList;
        $this->creditoEditar = $credito->id;
        $this->codigoEdit = $credito->code;
        $this->valorEditar = $credito->valor_solicitado;
        $this->cuotasEditar = $credito->cuotas_pagar;
        $this->fechaEditar = $credito->date_created;
        $this->tipoCreditoEditar = $prestamo->tipo;
        $this->prestamoEditar = $credito->tipo_prestamo;
        $this->porcentajeEditar = $prestamo->interes;
    }

    public function guardarEdicionPrestamo()
    {
        $createHeader = CreditFolderHeader::find($this->creditoEditar);
        $prestamos = Prestamos::find($this->prestamoEditar);
        if ($prestamos->diario == 0) {
            $this->generarNormalEdit();
        } else {
            $this->generarDiarioEdit();
        }
        $createHeader->porcentaje_interes_prestamo = $this->porcentajeEditar;
        $createHeader->valor_solicitado = $this->valorEditar;
        $createHeader->anios_pagar = $this->cuotasEditar / 12;
        $createHeader->cuotas_pagar = $this->cuotasEditar;
        $createHeader->valor_cuota = $this->valorCuotaEdit;
        $createHeader->valor_desgravamen = $this->valorDesgravamentEdi;
        $createHeader->date_created = $this->fechaEditar;
        $createHeader->hour_created = date("H:i:s");
        $createHeader->user_created_id = Auth::user()->id;
        $createHeader->user_created_name = Auth::user()->username;
        $createHeader->tipo_prestamo = $prestamos->id;
        $createHeader->save();
        CreditFolderDetail::where('code_folder_header', '=', $createHeader->code)->delete();
        foreach ($this->listaLetrasEdit as $letras) {
            $dataDetalle = [
                "numero_cuota" => $letras['cuotas'],
                "company_id" => Auth::user()->company_id,
                "code_folder_header" => $createHeader->code,
                "date_created" => $this->fecha_prestamo,
                "hour_created" => date("H:i:s"),
                "date_vencimiento" => $letras['fechas'],
                "interes_periodo" => $letras['interes'],
                "capital_amortizado" => $letras['amoritizado'],
                "fondo_desgravamen" => $letras['desgravamen'],
                "valor_cuota" => $letras['valMes'],
                "saldo_remanente" => $letras['deuda'],
            ];
            CreditFolderDetail::create($dataDetalle);
        }

        $color = 'info';
        $mensaje = 'Crédito Editado correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        $this->generarListadeCreditos();
        return;
    }

    public function generarNormalEdit()
    {
        $prestamos = Prestamos::find($this->prestamoEditar);
        $customer = Customer::find($this->customerID);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $this->fechaEditar;
        $interes = $this->porcentajeEditar;
        $deuda = $this->valorEditar;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        switch ($recuTiempo) {
            case 'D':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / 12;

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotasEditar)))) / ((pow((1 + $inte), ($this->cuotasEditar))) - 1);
                    } else {
                        $cuotas = $deuda / $this->cuotasEditar;
                    }


                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotasEditar), 2, '.', '');
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    $this->valorCuotaEdit = $valorCuotas;
                    $this->valorDesgravamentEdi = $fondoDesgravamen;
                    for ($i = 1; $i <= $this->cuotasEditar; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2, '.', '');
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => $valorCuotas,
                            'desgravamen' => $fondoDesgravamen,
                            'deuda' => number_format($deuda, 2, '.', ''),
                            'cuotaPago' => $valorCuotas,
                            'interes' => $interes,
                            'amoritizado' => number_format($amortizado, 2, '.', '')
                        ];
                    }
                } else {
                    $prestamo = $deuda; // cantidad del préstamo
                    $interes_anual = $interes; // tasa de interés anual
                    $plazo = $this->cuotasEditar; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / 12; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    $this->valorCuotaEdit = 0;
                    $this->valorDesgravamentEdi = $fondo_desgravamen;
                    for ($i = 1; $i <= $this->cuotasEditar; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $valInteres = $dineroCalculo * $interes_mensual;
                        $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                        $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                        $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                        $sumaInteres = $sumaInteres + $valInteres;
                        $dineroCalculo = $dineroCalculo - $amortizado;
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'desgravamen' => number_format($fondo_desgravamen, 2, '.', ''),
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / 12;

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotasEditar)))) / ((pow((1 + $inte), ($this->cuotasEditar))) - 1);
                    } else {
                        $cuotas = $deuda / $this->cuotasEditar;
                    }

                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotasEditar), 2, '.', '');
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    $this->valorCuotaEdit = $valorCuotas;
                    $this->valorDesgravamentEdi = $fondoDesgravamen;
                    for ($i = 1; $i <= $this->cuotasEditar; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addWeeks($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2, '.', '');
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => $valorCuotas,
                            'desgravamen' => $fondoDesgravamen,
                            'deuda' => number_format($deuda, 2, '.', ''),
                            'cuotaPago' => $valorCuotas,
                            'interes' => $interes,
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                } else {
                    $prestamo = $deuda; // cantidad del préstamo
                    $interes_anual = $interes; // tasa de interés anual
                    $plazo = $this->cuotasEditar; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / 12; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    $this->valorCuotaEdit = 0;
                    $this->valorDesgravamentEdi = $fondo_desgravamen;
                    for ($i = 1; $i <= $this->cuotasEditar; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addWeeks($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $valInteres = $dineroCalculo * $interes_mensual;
                        $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                        $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                        $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                        $sumaInteres = $sumaInteres + $valInteres;
                        $dineroCalculo = $dineroCalculo - $amortizado;
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'desgravamen' => number_format($fondo_desgravamen, 2, '.', ''),
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
            case 'M':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / 12;

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotasEditar)))) / ((pow((1 + $inte), ($this->cuotasEditar))) - 1);
                    } else {
                        $cuotas = $deuda / $this->cuotasEditar;
                    }



                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotasEditar), 2, '.', '');
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');
                    $this->valorCuotaEdit = $valorCuotas;
                    $this->valorDesgravamentEdi = $fondoDesgravamen;
                    for ($i = 1; $i <= $this->cuotasEditar; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addMonths($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2, '.', '');
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => $valorCuotas,
                            'desgravamen' => $fondoDesgravamen,
                            'deuda' => number_format($deuda, 2, '.', ''),
                            'cuotaPago' => $valorCuotas,
                            'interes' => $interes,
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                } else {
                    $prestamo = $deuda; // cantidad del préstamo
                    $interes_anual = $interes; // tasa de interés anual
                    $plazo = $this->cuotasEditar; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / 12; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    $this->valorCuotaEdit = 0;
                    $this->valorDesgravamentEdi = $fondo_desgravamen;
                    for ($i = 1; $i <= $this->cuotasEditar; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addMonths($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $valInteres = $dineroCalculo * $interes_mensual;
                        $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                        $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                        $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                        $sumaInteres = $sumaInteres + $valInteres;
                        $dineroCalculo = $dineroCalculo - $amortizado;
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
                            'valMes' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'desgravamen' => number_format($fondo_desgravamen, 2, '.', ''),
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
        }
        $this->interesSumaEdit = $sumaInteres;
        $this->listaLetrasEdit = $pagos;
    }

    public function generarDiarioEdit()
    {
        $prestamos = Prestamos::find($this->prestamoEditar);
        $customer = Customer::find($this->customerID);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $this->fechaEditar;
        $fechaPrestamoDiarios = $this->fechaEditar;
        $interes = $this->porcentajeEditar;
        $deuda = $this->valorEditar;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        $divider = 30;
        switch ($recuTiempo) {
            case 'D':
                if ($prestamos->tipo == 'F') {
                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $this->cuotasEditar / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2, '.', ''); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $this->cuotasEditar, 2, '.', '');
                    $cuotaPago = number_format($valorFinal / $this->cuotasEditar, 2, '.', ''); //valor de la cuota
                    $contador = 1;

                    while ($contador <= $this->cuotasEditar) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamo;
                        if (date("w", strtotime($fecha)) != 0) {

                            $pagos[] = [
                                'cuotas' => $contador,
                                'fechas' => $fechaPrestamo,
                                'valMes' => $cuotaPago,
                                'desgravamen' => 0,
                                'deuda' => 0,
                                'cuotaPago' => $cuotaPago,
                                'interes' => $interesPorLetra,
                                'amoritizado' => number_format(0, 2),
                            ];
                            $contador++;
                        }
                    }
                } else {
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {
                    $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                    $fechaMasDias = $fecha->addWeeks($this->cuotasEditar);
                    $fchaNueva = $fechaMasDias->toDateString();
                    //                    cantidad de diase netre las fechas
                    $fecha1 = Carbon::parse($fechaPrestamo);
                    $fecha2 = Carbon::parse($fchaNueva);

                    $diferenciaEnDias = $fecha1->diffInDays($fecha2);

                    $contador = 1;
                    $diasRestar = 0;
                    $diasSumar = 0;
                    while ($contador <= $diferenciaEnDias) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamo;
                        if (date("w", strtotime($fecha)) != 0) {
                            $diasSumar += 1;
                        } else {
                            $diasRestar += 1;
                        }
                        $contador++;
                    }
                    $recorrido = $diasSumar;

                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $recorrido / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2, '.', ''); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $recorrido, 2, '.', '');
                    $cuotaPago = number_format($valorFinal / $recorrido, 2, '.', ''); //valor de la cuota
                    $contadorLetras = 1;
                    while ($contadorLetras <= $recorrido) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamoDiarios);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamoDiarios = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamoDiarios;
                        if (date("w", strtotime($fecha)) != 0) {
                            $pagos[] = [
                                'cuotas' => $contadorLetras,
                                'fechas' => $fechaPrestamoDiarios,
                                'valMes' => $cuotaPago,
                                'desgravamen' => 0,
                                'deuda' => 0,
                                'cuotaPago' => $cuotaPago,
                                'interes' => $interesPorLetra,
                                'amoritizado' => number_format(0, 2),
                            ];
                            $contadorLetras++;
                        }
                    }
                } else {
                }
                break;
            case 'M':
                if ($prestamos->tipo == 'F') {
                    $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                    $fechaMasDias = $fecha->addMonths($this->cuotasEditar);
                    $fchaNueva = $fechaMasDias->toDateString();
                    //                    cantidad de diase netre las fechas
                    $fecha1 = Carbon::parse($fechaPrestamo);
                    $fecha2 = Carbon::parse($fchaNueva);
                    $diferenciaEnDias = $fecha1->diffInDays($fecha2);
                    $contador = 1;
                    $diasSumar = 0;
                    $diasRestar = 0;
                    while ($contador <= $diferenciaEnDias) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamo;
                        if (date("w", strtotime($fecha)) != 0) {
                            $diasSumar += 1;
                        } else {
                            $diasRestar += 1;
                        }
                        $contador++;
                    }
                    $recorrido = $diasSumar;
                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $recorrido / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2, '.', ''); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $recorrido, 2, '.', '');
                    $cuotaPago = number_format($valorFinal / $recorrido, 2, '.', ''); //valor de la cuota
                    $contadorLetras = 1;
                    while ($contadorLetras <= $recorrido) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamoDiarios);
                        $periodo = 1;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamoDiarios = $fechaMasDias->toDateString();
                        $fecha = $fechaPrestamoDiarios;
                        if (date("w", strtotime($fecha)) != 0) {
                            $pagos[] = [
                                'cuotas' => $contadorLetras,
                                'fechas' => $fechaPrestamoDiarios,
                                'valMes' => $cuotaPago,
                                'desgravamen' => 0,
                                'deuda' => 0,
                                'cuotaPago' => $cuotaPago,
                                'interes' => $interesPorLetra,
                                'amoritizado' => number_format(0, 2),
                            ];
                            $contadorLetras++;
                        }
                    }
                } else {
                }
                break;
        }
        $this->interesSumaEdit = $interesLetras;
        $this->listaLetrasEdit = $pagos;
    }

    public function verficarExisteCuentaDescargos($credito_id, $id)
    {
        $respuesta = false;
        $credito = CreditFolderHeader::find($credito_id);
        $customer = Customer::find($credito->customer_id);
        $cuenta = CustomerTipoAhorros::join('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', 'tipo_ahorros.id')
            ->where('tipo_ahorros.descargo_creditos', true)
            ->where('customer_tipo_ahorros.company_id', Auth::user()->company_id)
            ->where('customer_tipo_ahorros.customer_id', $customer->id)
            ->where('customer_tipo_ahorros.id', $id)
            ->first();
        if ($cuenta) {
            $respuesta = true;
        }
        return $respuesta;
    }

    public function movimientoCreditoCuentaEmpresa($credito_id, $lleva_contabilidad, $cuenta_contable_haber)
    {
        $credito = CreditFolderHeader::find($credito_id);
        if (!$credito) {
            throw new \Exception('No existe el credito para generar el movimiento de entrega.');
        }

        $customer = Customer::find($credito->customer_id);
        if (!$customer) {
            throw new \Exception('No existe el cliente del credito para generar el movimiento de entrega.');
        }

        $operacion = "ENC";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->where('action', 'R')
            ->where('afecta', 'E')
            ->first();
        if (!$transaction) {
            throw new \Exception('No existe el tipo de transaccion ENC configurado.');
        }

        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $prestamos = Prestamos::find($credito->tipo_prestamo);
        if (!$prestamos) {
            throw new \Exception('El credito no tiene un tipo de prestamo valido configurado.');
        }

        $dineroEntregar = $credito->valor_solicitado;
        if ($prestamos->suma_valores_gastos_prestamo) {
            $dineroEntregar = ($credito->valor_solicitado) - $credito->encaje_valor - $credito->gasto_administrativo;

            //if ($prestamos->letra_credito == 'LETRA') {
            //    $dineroEntregar = $credito->valor_solicitado;
            //} else {
            //    $dineroEntregar = ($credito->valor_solicitado) - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto  -  $credito->segundo_gasto  - $credito->tercer_gasto;
            //}
        } else if ($prestamos->suma_valores_gastos_prestamo == false) {
            //$dineroEntregar = $credito->valor_solicitado - $credito->encaje_valor - $credito->gasto_administrativo;
            $dineroEntregar = $credito->valor_solicitado - $credito->suma_valores_gastos_prestamo - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto - $credito->segundo_gasto - $credito->tercer_gasto;
        } else {
            $dineroEntregar = $credito->valor_solicitado - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto - $credito->segundo_gasto - $credito->tercer_gasto;
        }
        /*
        if ($prestamos->suma_valores_gastos_prestamo) {
            if ($prestamos->letra_credito == 'LETRA') {
                $dineroEntregar = $credito->valor_solicitado;
            } else {
                $dineroEntregar = ($credito->valor_solicitado) - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto  -  $credito->segundo_gasto  - $credito->tercer_gasto;
            }
        } else if ($credito->suma_valores_gastos_prestamo > 0) {
            $dineroEntregar = $credito->valor_solicitado - $credito->encaje_valor - $credito->gasto_administrativo;
        } else {
            $dineroEntregar = $credito->valor_solicitado - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto - $credito->segundo_gasto - $credito->tercer_gasto;
        }*/


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
            "valor_movimiento" => $dineroEntregar,
            "credit_folder_header_id" => $credito_id,
            "saldo_general" => $valorTotal + $dineroEntregar,
            "observation" => 'DEBITO DEL CAPITAL PARA EL CREDITO ' . $credito->id . ' DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "banco_id" => $cuenta_contable_haber,
            "automatico" => $lleva_contabilidad,
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $dineroEntregar, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
        BaseController::enviarMail($movimientos);
        $color = 'success';
        $mensaje = 'El crédito salio correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        return $movimientos;
    }

    public function movimientoCreditoCuentaCliente($credito_id, $idCuenta)
    {
        $credito = CreditFolderHeader::find($credito_id);
        $customer = Customer::find($credito->customer_id);
        $cuenta = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $customer->id)
            ->where('id', $idCuenta)
            ->first();
        $operacion = "IN";
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', $operacion)
            ->where('action', 'S')
            ->where('afecta', 'C')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();

        $prestamos = Prestamos::find($credito->tipo_prestamo);
        $dineroEntregar = $credito->valor_solicitado;
        if ($prestamos->suma_valores_gastos_prestamo) {
            if ($prestamos->letra_credito == 'LETRA') {
                $dineroEntregar = $credito->valor_solicitado;
            } else {
                $dineroEntregar = ($credito->valor_solicitado) - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto  -  $credito->segundo_gasto  - $credito->tercer_gasto;
            }
        } else if ($credito->suma_valores_gastos_prestamo > 0) {
            $dineroEntregar = $credito->valor_solicitado - $credito->encaje_valor - $credito->gasto_administrativo;
        } else {
            $dineroEntregar = $credito->valor_solicitado - $credito->encaje_valor - $credito->gasto_administrativo - $credito->primer_gasto - $credito->segundo_gasto - $credito->tercer_gasto;
        }

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
            "customer_tipo_ahorro_id" => $cuenta->id,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $dineroEntregar,
            "saldo_general" => $valorTotal + $dineroEntregar,
            "credit_folder_header_id" => $credito_id,
            "observation" => 'DESCARGO AUTOMÁTICO EN CUENTA DE AHORROS POR EL CREDITO ' . $credito->id . ' DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "banco_id" => $this->banco_id,
        ];
        
        $movimientos = CustomerMovimiento::create($data);
        //dd('entra , movimiento creado');
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $dineroEntregar, $customer->code, $movimientos->saldo_general, date('Y-m-d'), $movimientos->customer_tipo_ahorro_id);
        BaseController::guardarValoresCartola($cuenta->id, $customer->id, $movimientos, $transaction);
        BaseController::enviarMail($movimientos);

        $whatsappEnviar = 'Estimad@, *' . $customer->nombres . ' ' . $customer->apellidos . '* tu transacción ha sido procesada exitosamente, por el monto de: *' . $dineroEntregar . '$* Gracias por usar nuestros servicios de .' . Auth::user()->company->comercial_name;
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

        $color = 'success';
        $mensaje = 'El valor fue acreditado correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function descargarBalorBoveda($id)
    {
        $credito = CreditFolderHeader::find($id);
        $bovedas = Bovedas::where('status', 1)->where('principal', 1)->first();
        if ($bovedas) {
            $operacion = OperacionesDescargoBovedas::where('nombre_corto', 'EVC')->first();
            $solicitud = new DescargoBovedasHeader();
            $solicitud->company_id = Auth::user()->company_id;
            $solicitud->credit_folder_header_id = $id;
            $solicitud->operaciones_descargo_bovedas_id = $operacion->id;
            $solicitud->boveda_origen_id = $bovedas->id;
            $solicitud->boveda_destino_id = 0;
            $solicitud->valor = $credito->valor_solicitado;
            $solicitud->observacion = $operacion->descripcion . ' numero ' . $credito->code;
            $solicitud->fecha_creacion = date('Y-m-d');
            $solicitud->estado = 'FINALIZADO';
            $solicitud->bancos_id = ($this->obligarCuentaInterna == 1) ? $this->banco_id : null;
            $solicitud->save();
        } else {
            $color = 'danger';
            $mensaje = 'NO tiene una bóveda para descargar el credito';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            $this->dispatchBrowserEvent('closeModal');
            $this->generarListadeCreditos();

            return;
        }
    }

    public function negarCredito($id)
    {
        $credito = CreditFolderHeader::find($id);
        $credito->date_cancel = date('Y-m-d');
        $credito->hour_cancel = date("H:i:s");
        $credito->user_cancel_id = Auth::user()->id;
        $credito->user_cancel = Auth::user()->username;
        $credito->status = 'NEGADO';
        $credito->save();
        $this->seleccionarCliente($this->id_seleccionado);
    }

    public function abrirCreditos()
    {
        $this->mostrarSimulador = true;
        $this->mostrarListaCreditos = false;
        $this->mostrarCalificacion = false;
        $this->styleMostrarSimulador = 'btn-primary';
        $this->StyleMostrarListaCreditos = '';
        $this->StyleMostrarCalificacion = '';
        $this->garantesArreglo = [];
    }

    public function abrirListaCreditos()
    {
        $this->mostrarSimulador = false;
        $this->mostrarListaCreditos = true;
        $this->mostrarCalificacion = false;
        $this->styleMostrarSimulador = '';
        $this->StyleMostrarListaCreditos = 'btn-primary';
        $this->StyleMostrarCalificacion = '';
        $this->generarListadeCreditos();
    }
    public function abrirCalificacion()
    {

        $this->mostrarSimulador = false;
        $this->mostrarListaCreditos = false;
        $this->mostrarCalificacion = true;
        $this->styleMostrarSimulador = '';
        $this->StyleMostrarListaCreditos = '';
        $this->StyleMostrarCalificacion = 'btn-primary';
    }

    public function calcularCalificacion()
    {
        if ($this->customer_selec == 0) {
            $color = 'danger';
            $mensaje = 'Debe seleccionar un clinete';
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
                'ingresos_netos' => 'required',
                'gastos_mensuales' => 'required',
                'tasa_interes' => 'required',
                'plazo_prestamo' => 'required',
                'activos' => 'required',
                'pasivos' => 'required',
            ],
            [
                'ingresos_netos.required' => 'Necesita ingresar los Ingresos Netos.',
                'gastos_mensuales.required' => 'Necesita ingresar los Gastos Mensuales.',
                'tasa_interes.required' => 'Necesita seleccionar un prestamo para calcular la tasa de interes.',
                'plazo_prestamo.required' => 'Necesita ingresar un plazo del credito.',
                'activos.required' => 'Necesita ingresar el valor de los activos.',
                'pasivos.required' => 'Necesita ingresar el valor de los pasivos.',
            ]
        );
        $prestamosListado = Prestamos::find($this->tasa_interes);
        $ingresosNetos = $this->ingresos_netos;
        $gastosMensuales = $this->gastos_mensuales;
        $tasaInteresAnual = $prestamosListado->interes / 100;
        $plazoPrestamoEnAnios = $this->plazo_prestamo;
        $activos = $this->activos;
        $pasivos = $this->pasivos;

        // Calcular el patrimonio
        $patrimonio = $activos - $pasivos;

        // Calcular el pago máximo mensual de deuda (30% del ingreso neto mensual)
        $pagoMaximoMensualDeDeuda = $ingresosNetos * 0.30;

        // Calcular la capacidad de endeudamiento usando la fórmula de anualidad
        $numPagosPorAno = 12; // Número de pagos por año
        $plazoEnMeses = $plazoPrestamoEnAnios * $numPagosPorAno;
        $tasaInteresMensual = $tasaInteresAnual / $numPagosPorAno;

        // Fórmula para calcular el monto del préstamo (P)
        $capacidadDeEndeudamiento = $pagoMaximoMensualDeDeuda / (($tasaInteresMensual * pow((1 + $tasaInteresMensual), $plazoEnMeses)) / (pow((1 + $tasaInteresMensual), $plazoEnMeses) - 1));

        $this->datosCalculados = [
            'ingresos_netos' => $ingresosNetos,
            'gastos_mensuales' => $gastosMensuales,
            'pago_maximo_mensual_de_deuda' => $pagoMaximoMensualDeDeuda,
            'capacidad_de_endeudamiento' => $capacidadDeEndeudamiento,
            'activos' => $activos,
            'pasivos' => $pasivos,
            'patrimonio' => $patrimonio
        ];
        $this->ingresos_netos = $ingresosNetos;
        $this->gastos_mensuales = $gastosMensuales;
        $this->pago_maximo_mensual_de_deuda = $pagoMaximoMensualDeDeuda;
        $this->capacidad_de_endeudamiento = $capacidadDeEndeudamiento;
        $this->activos = $activos;
        $this->pasivos = $pasivos;
        $this->patrimonio = $patrimonio;
    }

    public function seleccionarCredito($id)
    {
        $this->id_credito = $id;
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
        $customerFile->customer_id = $this->id_seleccionado;
        $customerFile->credit_header_id = $this->id_credito;
        $customerFile->descripcion = $this->descripcion ?? 'Archivo sin descripción';
        $customerFile->formato = $extension;
        $customerFile->path = $url;
        $customerFile->archivo = $ruta;
        $customerFile->save();
        $this->limpiarFormulario();
        $this->generarListadeCreditos();
        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Notificación',
            'color' => 'success',
            'mensaje' => 'Se guardó correctamente'
        ]);
    }

    private function limpiarFormulario()
    {
        $this->reset(['archivo', 'descripcion']);
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount($customer_id)
    {
        if ($customer_id > 0) {
            $customerFind = Customer::where('company_id', Auth::user()->company_id)->findOrFail($customer_id);
            if ($customerFind != null) {
                $this->seleccionarCliente($customer_id);
                $this->customer_selec = $customer_id;
                $this->search = $customerFind->numero_documento;
                $this->mostrarListaCreditos = true;
                $this->StyleMostrarListaCreditos = 'btn-primary';
            }
        }
    }

    public function liquidarDeuda($id)
    {
        $this->capitalLiquidar = '';
        $this->desgravamenLiquidar = '';
        $this->interesLiquidar = '';
        $this->totalLiquidar = '';
        $this->feacha_actualLiquidar = '';
        $this->detalleLiquidar = [];
        $this->formasPagarCreditoLiquidar = [];
        $this->carpetaLiquidar = '';
        $header = CreditFolderHeader::find($id);
        if ($header->cuotas_pagar <= 12) {
            $cuotas = 6;
        } else {
            if (($header->cuotas_pagar % 2) == 0) {
                $cuotas = $header->cuotas_pagar / 2;
            } else {
                $cuotas = ((int) ($header->cuotas_pagar / 2)) + 1;
            }
        }
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($detalle as $detail) {
            if ($detail->numero_cuota <= $cuotas) {
                $detail->cobro = true;
            } else {
                $detail->cobro = false;
            }
        }
        $capital = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->sum('capital_amortizado');
        $desgravamen = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->sum('fondo_desgravamen');
        $interes = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->where('numero_cuota', '<=', $cuotas)
            ->sum('interes_periodo');
        $total = (float) round(($capital + $desgravamen + $interes), 2);

        $this->capitalLiquidar = (float) round($capital, 2);
        $this->desgravamenLiquidar = (float) round($desgravamen, 2);
        $this->interesLiquidar = (float) round($interes, 2);
        $this->totalLiquidar = $total;
        $this->feacha_actualLiquidar = date('Y-m-d');
        $this->detalleLiquidar = $detalle;
        $this->carpetaLiquidar = $id;
    }

    /*public function guardarLiquidacion()
    {
        $valorPagado = 0;
        foreach ($this->formasPagarCreditoLiquidar as $formasPago) {
            $valorPagado += $formasPago['valor'];
        }
        if ($valorPagado == $this->totalLiquidar) {
            $color = 'info';
            $mensaje = 'Los valores son correctos';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        } else {
            $color = 'danger';
            $mensaje = 'El valor a pagar no es el mismo que el de las formas de págo';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $cajaAbierta = Cajas::where('status', 'ABIERTA')
            ->where('date_inicial', date('Y-m-d'))
            ->where('user_inicial_id', Auth::user()->id)
            ->count();
        if ($cajaAbierta == 1) {
            $this->verBotonlistaPrestamos = true;
        } else {
            $this->verBotonlistaPrestamos = false;
        }
        if ($this->verBotonlistaPrestamos == false) {
            return;
        }


        $id = $this->carpetaLiquidar;
        $header = CreditFolderHeader::find($id);
        if ($header->cuotas_pagar <= 12) {
            $cuotas = 6;
        } else {
            if (($header->cuotas_pagar % 2) == 0) {
                $cuotas = $header->cuotas_pagar / 2;
            } else {
                $cuotas = ((int) ($header->cuotas_pagar / 2)) + 1;
            }
        }
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($detalle as $det) {
            $detalle = CreditFolderDetail::find($det->id);
            $capital = $det->capital_amortizado;
            $desgravamen = $det->fondo_desgravamen;
            if ($det->numero_cuota <= $cuotas) {
                $interes = $det->interes_periodo;
            } else {
                $interes = 0;
            }
            $pagoRealizado = (float) round(($capital + $desgravamen + $interes), 2);
            $detalle->valor_pagado = $pagoRealizado;
            $detalle->valor_final = $pagoRealizado;
            //$detalle->tipo_pago = 'EFECTIVO';

            $detalle->tipo_pago = $formasPago['formaName'];

            if ($formasPago['formaName'] == 'TRANSFERENCIA') {
                $detalle->banco_id = $formasPago['banco_pago_id'];
                $detalle->numero_comprobante = $formasPago['numero_comprobante'];
            } else {
                $detalle->banco_id = null;
                $detalle->numero_comprobante = null;
            }

            //En el Caso de PAGOS MIXTOS
            /*
            $formaPago = $this->formasPagarCreditoLiquidar[0];

            $detalle->tipo_pago = $formaPago['formaName'];

            if ($formaPago['formaName'] == 'TRANSFERENCIA') {
                $detalle->banco_id = $formaPago['banco_pago_id'];
                $detalle->numero_comprobante = $formaPago['numero_comprobante'];
            } else {
                $detalle->banco_id = null;
                $detalle->numero_comprobante = null;
            }

            $detalle->obervation_pago = 'PAGA LA TOTALIDAD DEL CREDITO ' . date('Y-m-d');
            $detalle->date_pay = date('Y-m-d');
            $detalle->hour_pay = date('H:i:s');
            $detalle->user_pay_id = Auth::user()->id;
            $detalle->status = 'PAGADA';
            $detalle->save();
        }
        $customer = Customer::find($header->customer_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'LIC')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
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
            "valor_movimiento" => $valorPagado,
            "saldo_general" => $valorTotal + $valorPagado,
            "observation" => 'PAGA LA TOTALIDAD DEL CREDITO ' . $header->code . ' EN LA FECHA ' . date('Y-m-d'),
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorPagado, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
        $cabecera = CreditFolderHeader::find($id);
        $valornew = $cabecera->total_pagando + $this->totalLiquidar;
        $cabecera->total_pagando = $valornew;
        $cabecera->status = 'PAGADO';
        $cabecera->save();
    }*/

    /*public function guardarLiquidacion()
    {
        $valorPagado = 0;

        foreach ($this->formasPagarCreditoLiquidar as $formasPago) {
            $valorPagado += (float) $formasPago['valor'];
        }

        $valorPagado = (float) round($valorPagado, 2);
        $totalLiquidar = (float) round($this->totalLiquidar, 2);

        if ($valorPagado != $totalLiquidar) {
            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'El valor a pagar no es el mismo que el de las formas de pago'
            ];

            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        if (empty($this->formasPagarCreditoLiquidar)) {
            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'Debe ingresar al menos una forma de pago'
            ];

            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $cajaAbierta = Cajas::where('status', 'ABIERTA')
            ->where('date_inicial', date('Y-m-d'))
            ->where('user_inicial_id', Auth::user()->id)
            ->count();

        if ($cajaAbierta == 1) {
            $this->verBotonlistaPrestamos = true;
        } else {
            $this->verBotonlistaPrestamos = false;
        }

        if ($this->verBotonlistaPrestamos == false) {
            return;
        }

        $id = $this->carpetaLiquidar;

        $header = CreditFolderHeader::find($id);

        if (!$header) {
            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del crédito'
            ];

            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');

        $usuarioId = Auth::user()->id;
        $usuarioNombre = Auth::user()->username;


        $existeTransferencia = collect($this->formasPagarCreditoLiquidar)
            ->contains(function ($formaPago) {
                return strtoupper(trim($formaPago['formaName'])) === 'TRANSFERENCIA';
            });

        $estadoSolicitud = $existeTransferencia ? 3 : 0;

        foreach ($this->formasPagarCreditoLiquidar as $formasPago) {

            $formaNombre = strtoupper(trim($formasPago['formaName']));

            $dataSolicitud = [
                'company_id' => Auth::user()->company_id,
                'customer_movimientos_id' => null,
                'customer_id' => $header->customer_id,
                'liquidacion_id' => $header->id,
                'liquidacion' => 1,
                'forma_pago' => $formasPago['formaName'],
                'forma_pago_id' => $formasPago['formaId'],
                'banco_id' => $formaNombre === 'TRANSFERENCIA'
                    ? ($formasPago['banco_pago_id'] ?? null)
                    : null,
                'valor' => (float) $formasPago['valor'],
                'numero_comprobante' => $formaNombre === 'TRANSFERENCIA'
                    ? ($formasPago['numero_comprobante'] ?? null)
                    : null,
                'fecha_comprobante' => null,
                'hora_comprobante' => null,
                'date_create' => $fechaActual,
                'hour_create' => $horaActual,
                'user_id' => $usuarioId,
                'user_name' => $usuarioNombre,
                'status' => 1,
                'solicitado' => $estadoSolicitud,
                'usuario_solicitud' => null,
                'usuario_id_solicitud' => null,
                'fecha_solicitud' => $fechaActual,
                'hora_solicitud' => $horaActual,
                'observacion' => $existeTransferencia
                    ? 'SOLICITUD DE LIQUIDACIÓN DEL CRÉDITO ' . $header->code
                    : 'REGISTRO DE RESPALDO DE LIQUIDACIÓN DEL CRÉDITO ' . $header->code,
            ];

            RegistroFormasLiquidacion::create($dataSolicitud);
        }

        if ($existeTransferencia) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'info',
                'mensaje' => 'La solicitud de liquidación fue enviada correctamente para su aprobación'
            ];

            $this->dispatchBrowserEvent('alerta', $data);

            $this->formasPagarCreditoLiquidar = [];

            return;
        }

        if ($header->cuotas_pagar <= 12) {
            $cuotas = 6;
        } else {
            if (($header->cuotas_pagar % 2) == 0) {
                $cuotas = $header->cuotas_pagar / 2;
            } else {
                $cuotas = ((int) ($header->cuotas_pagar / 2)) + 1;
            }
        }

        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->get();

        $formasPagoNombres = collect($this->formasPagarCreditoLiquidar)
            ->pluck('formaName')
            ->unique()
            ->implode(' + ');

        foreach ($detalle as $det) {

            $capital = $det->capital_amortizado;
            $desgravamen = $det->fondo_desgravamen;

            if ($det->numero_cuota <= $cuotas) {
                $interes = $det->interes_periodo;
            } else {
                $interes = 0;
            }

            $pagoRealizado = (float) round(
                ($capital + $desgravamen + $interes),
                2
            );

            $det->valor_pagado = $pagoRealizado;
            $det->valor_final = $pagoRealizado;
            $det->tipo_pago = $formasPagoNombres;
            $det->banco_id = null;
            $det->numero_comprobante = null;
            $det->obervation_pago = 'PAGA LA TOTALIDAD DEL CREDITO ' . $fechaActual;
            $det->date_pay = $fechaActual;
            $det->hour_pay = $horaActual;
            $det->user_pay_id = $usuarioId;
            $det->status = 'PAGADA';

            $det->save();
        }

        $customer = Customer::find($header->customer_id);

        if (!$customer) {
            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del cliente'
            ];

            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'LIC')
            ->first();

        if (!$transaction) {
            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la transacción de liquidación'
            ];

            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $tabla = 'customer_movimientos';

        $code = BaseController::generarCodigo($tabla, 9);

        $valorTotal = BaseController::valorTotal();

        $data = [
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
            'valor_movimiento' => $valorPagado,
            'saldo_general' => $valorTotal + $valorPagado,
            'observation' => 'PAGA LA TOTALIDAD DEL CREDITO ' . $header->code . ' EN LA FECHA ' . $fechaActual,
            'user_created_id' => $usuarioId,
            'date_created' => $fechaActual,
            'hour_created' => $horaActual,
        ];

        $movimientos = CustomerMovimiento::create($data);

        RegistroFormasLiquidacion::where('liquidacion_id', $header->id)
            ->where('customer_id', $header->customer_id)
            ->where('solicitado', 0)
            ->where('status', 1)
            ->update([
                'customer_movimientos_id' => $movimientos->id,
                
            ]);

        CustomerHistorialController::guardarHistorialAutomatica(
            $movimientos->code,
            $transaction->id,
            $valorPagado,
            $customer->code,
            $movimientos->saldo_general,
            $fechaActual
        );

        $cabecera = CreditFolderHeader::find($id);

        $valornew = $cabecera->total_pagando + $this->totalLiquidar;

        $cabecera->total_pagando = $valornew;
        $cabecera->status = 'PAGADO';

        $cabecera->save();

        $data = [
            'titulo' => 'Notificación',
            'color' => 'info',
            'mensaje' => 'La liquidación fue realizada correctamente'
        ];

        $this->dispatchBrowserEvent('alerta', $data);

        $this->formasPagarCreditoLiquidar = [];
    }*/

    public function guardarLiquidacion()
    {
        $valorPagado = 0;

        foreach ($this->formasPagarCreditoLiquidar as $formasPago) {
            $valorPagado += (float) $formasPago['valor'];
        }

        $valorPagado = (float) round($valorPagado, 2);
        $totalLiquidar = (float) round($this->totalLiquidar, 2);

        if ($valorPagado != $totalLiquidar) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'El valor a pagar no es el mismo que el de las formas de pago'
            ];

            $this->dispatchBrowserEvent('alerta', $data);

            return;
        }

        if (empty($this->formasPagarCreditoLiquidar)) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'Debe ingresar al menos una forma de pago'
            ];

            $this->dispatchBrowserEvent('alerta', $data);

            return;
        }

        $cajaAbierta = Cajas::where('status', 'ABIERTA')
            ->where('date_inicial', date('Y-m-d'))
            ->where('user_inicial_id', Auth::user()->id)
            ->count();

        if ($cajaAbierta == 1) {

            $this->verBotonlistaPrestamos = true;
        } else {

            $this->verBotonlistaPrestamos = false;
        }

        if ($this->verBotonlistaPrestamos == false) {
            return;
        }
        $id = $this->carpetaLiquidar;

        $header = CreditFolderHeader::find($id);

        if (!$header) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'danger',
                'mensaje' => 'No se encontró la información del crédito'
            ];

            $this->dispatchBrowserEvent('alerta', $data);

            return;
        }

        $fechaActual = date('Y-m-d');
        $horaSolicitud = date('H:i:s');

        $usuarioId = Auth::user()->id;

        $usuarioNombre = Auth::user()->username;


        $existeTransferencia = collect(
            $this->formasPagarCreditoLiquidar
        )->contains(function ($formaPago) {

            return strtoupper(
                trim($formaPago['formaName'])
            ) === 'TRANSFERENCIA';
        });

        $estadoSolicitud = $existeTransferencia ? 3 : 0;

        foreach ($this->formasPagarCreditoLiquidar as $formasPago) {

            $formaNombre = strtoupper(
                trim($formasPago['formaName'])
            );


            $dataSolicitud = [

                'company_id' =>
                Auth::user()->company_id,

                'customer_movimientos_id' => null,

                'customer_id' =>
                $header->customer_id,

                'liquidacion_id' =>
                $header->id,

                'liquidacion' => 1,

                'forma_pago' =>
                $formasPago['formaName'],

                'forma_pago_id' =>
                $formasPago['formaId'],

                'banco_id' =>
                $formaNombre === 'TRANSFERENCIA'
                    ? ($formasPago['banco_pago_id'] ?? null)
                    : null,

                'valor' =>
                (float) $formasPago['valor'],

                'numero_comprobante' =>
                $formaNombre === 'TRANSFERENCIA'
                    ? ($formasPago['numero_comprobante'] ?? null)
                    : null,

                'fecha_comprobante' => null,

                'hora_comprobante' => null,

                'date_create' =>
                $fechaActual,

                'hour_create' =>
                $horaSolicitud,

                'user_id' =>
                $usuarioId,

                'user_name' =>
                $usuarioNombre,

                'status' => 1,

                'solicitado' =>
                $estadoSolicitud,

                'usuario_solicitud' => null,

                'usuario_id_solicitud' => null,

                'fecha_solicitud' =>
                $fechaActual,

                'hora_solicitud' =>
                $horaSolicitud,

                'observacion' =>
                $existeTransferencia
                    ? 'SOLICITUD DE LIQUIDACIÓN DEL CRÉDITO ' . $header->code
                    : 'REGISTRO DE RESPALDO DE LIQUIDACIÓN DEL CRÉDITO ' . $header->code,
            ];


            RegistroFormasLiquidacion::create(
                $dataSolicitud
            );
        }

        if ($existeTransferencia) {

            $data = [
                'titulo' => 'Notificación',
                'color' => 'info',
                'mensaje' => 'La solicitud de liquidación fue enviada correctamente para su aprobación'
            ];

            $this->dispatchBrowserEvent(
                'alerta',
                $data
            );

            $this->formasPagarCreditoLiquidar = [];

            return;
        }

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
            ->get();


        $formasPagoNombres = collect(
            $this->formasPagarCreditoLiquidar
        )
            ->pluck('formaName')
            ->unique()
            ->implode(' + ');

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

            $det->banco_id =
                null;

            $det->numero_comprobante =
                null;

            $det->obervation_pago =
                'PAGA LA TOTALIDAD DEL CREDITO ' .
                $header->code .
                ' ' .
                $fechaActual;

            $det->date_pay =
                $fechaActual;

            $det->hour_pay =
                $horaSolicitud;

            $det->user_pay_id =
                $usuarioId;

            $det->status =
                'PAGADA';


            $det->save();
        }


        $customer =
            Customer::find(
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

        $transaction =
            TypeTransaction::where(
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

        $tabla =
            'customer_movimientos';

        $code =
            BaseController::generarCodigo(
                $tabla,
                9
            );

        $valorTotal =
            BaseController::valorTotal();


        $data = [

            'code' =>
            $code,

            'company_id' =>
            Auth::user()->company_id,

            'customer_code' =>
            $customer->code,

            'afecta' =>
            $transaction->afecta,

            'customer_name' =>
            $customer->nombres .
                ' ' .
                $customer->apellidos,

            'customer_ruc' =>
            $customer->numero_documento,

            'customer_address' =>
            $customer->direccion,

            'customer_telefono' =>
            $customer->telefono,

            'type_transaction_id' =>
            $transaction->id,

            'type_transaction_name' =>
            $transaction->name,

            'type_transaction_action' =>
            $transaction->action,

            'valor_movimiento' =>
            $valorPagado,

            'saldo_general' =>
            $valorTotal +
                $valorPagado,

            'observation' =>
            'PAGA LA TOTALIDAD DEL CREDITO ' .
                $header->code .
                ' EN LA FECHA ' .
                $fechaActual,

            'user_created_id' =>
            $usuarioId,

            'date_created' =>
            $fechaActual,

            'hour_created' =>
            $horaSolicitud,
        ];


        $movimientos =
            CustomerMovimiento::create(
                $data
            );

        RegistroFormasLiquidacion::where(
            'liquidacion_id',
            $header->id
        )
            ->where(
                'customer_id',
                $header->customer_id
            )
            ->where(
                'fecha_solicitud',
                $fechaActual
            )
            ->where(
                'hora_solicitud',
                $horaSolicitud
            )
            ->where(
                'solicitado',
                0
            )
            ->where(
                'status',
                1
            )
            ->update([

                'customer_movimientos_id' =>
                $movimientos->id,

            ]);

        CustomerHistorialController::guardarHistorialAutomatica(

            $movimientos->code,

            $transaction->id,

            $valorPagado,

            $customer->code,

            $movimientos->saldo_general,

            $fechaActual

        );

        $cabecera =
            CreditFolderHeader::find(
                $id
            );


        $valornew =
            $cabecera->total_pagando +
            $this->totalLiquidar;


        $cabecera->total_pagando =
            $valornew;

        $cabecera->status =
            'PAGADO';


        $cabecera->save();

        $data = [

            'titulo' =>
            'Notificación',

            'color' =>
            'info',

            'mensaje' =>
            'La liquidación fue realizada correctamente'

        ];


        $this->dispatchBrowserEvent(
            'alerta',
            $data
        );


        $this->formasPagarCreditoLiquidar = [];
    }


    public function agregarFormaPago()
    {

        $formasPago = FormasPago::find($this->formaPago);


        if ($formasPago->nombre == 'TRANSFERENCIA') {
            $this->validate(
                [
                    'formaPago' => 'required',
                    'valor_forma_pago' => 'required|numeric|min:0.01',
                    'banco_pago_id'    => 'required',
                ],
                [
                    'formaPago.required' => 'Necesita seleccionar una forma de pago.',
                    'valor_forma_pago.required' => 'Necesita Ingresar un valor para la forma de pago.',


                    'valor_forma_pago.numeric'  => 'El valor debe ser numérico.',
                    'valor_forma_pago.min'      => 'El valor debe ser mayor a 0.',
                    'banco_pago_id.required'    => 'Debe seleccionar un banco.',
                ]
            );
        } else {
            $this->validate(
                [
                    'formaPago' => 'required',
                    'valor_forma_pago' => 'required|numeric|min:0.01',

                ],
                [
                    'formaPago.required' => 'Necesita seleccionar una forma de pago.',
                    'valor_forma_pago.required' => 'Necesita Ingresar un valor para la forma de pago.',


                    'valor_forma_pago.numeric'  => 'El valor debe ser numérico.',
                    'valor_forma_pago.min'      => 'El valor debe ser mayor a 0.',

                ]
            );
        }

        $formasPago = FormasPago::find($this->formaPago);

        if ($formasPago->nombre == 'TRANSFERENCIA') {
            $this->validate(
                [
                    'numero_comprobante' => 'required',
                ],
                [
                    'numero_comprobante.required' => 'Debe ingresar el número de comprobante.',
                ]
            );
        }

        $nuevoDato = [
            "formaId" => $formasPago->id,
            "formaName" => $formasPago->nombre,
            "valor" => $this->valor_forma_pago,
            "banco_pago_id" => $this->banco_pago_id,
            "numero_comprobante" => $formasPago->nombre == 'TRANSFERENCIA'  ? $this->numero_comprobante : null,
        ];

        $this->formasPagarCredito[] = $nuevoDato;

        $this->reset([
            'valor_forma_pago',
            'banco_pago_id',
            'numero_comprobante'
        ]);
    }

    public function agregarGarante()
    {
        $this->validate(
            [
                'garante' => 'required',
            ],
            [
                'garante.required' => 'Necesita seleccionar un garante.',
            ]
        );
        $garante = Customer::find($this->garante);
        $nuevoGarante = [
            'customerId' => $garante->id,
            'customerName' => $garante->apellidos . ' ' . $garante->nombres,
            'customerIdentificacion' => $garante->numero_documento,
            'customerConyugeNombre' => $garante->conyugue_nombre,
            'customerConyugeIdentificacion' => $garante->conyugue_identificacion,
        ];
        $this->garantesArreglo[] = $nuevoGarante;
    }

    public function agregarFormaPagoLiquidar()
    {
        $formasPago = FormasPago::find($this->formaPagoLiquidar);

        if ($formasPago->nombre == 'TRANSFERENCIA') {

            $this->validate([
                'formaPagoLiquidar' => 'required',
                'valor_forma_pago_liquidar' => 'required|numeric|min:0.01',
                'banco_pago_id' => 'required',
                'numero_comprobante' => 'required',
            ]);
        } else {

            $this->validate([
                'formaPagoLiquidar' => 'required',
                'valor_forma_pago_liquidar' => 'required|numeric|min:0.01',
            ]);
        }
        $formasPago = FormasPago::find($this->formaPagoLiquidar);
        $nuevoDato = [
            "formaId" => $formasPago->id,
            "formaName" => $formasPago->nombre,
            "valor" => $this->valor_forma_pago_liquidar,
            "banco_pago_id" => $this->banco_pago_id,
            "numero_comprobante" => $formasPago->nombre == 'TRANSFERENCIA'
                ? $this->numero_comprobante
                : null,
        ];

        $this->formasPagarCreditoLiquidar[] = $nuevoDato;
        $this->valor_forma_pago_liquidar = '';

        $this->reset([
            'formaPagoLiquidar',
            'valor_forma_pago_liquidar',
            'banco_pago_id',
            'numero_comprobante'
        ]);
    }

    public function eliminarFormaPagar($index)
    {
        unset($this->formasPagarCredito[$index]);
        $this->formasPagarCredito = array_values($this->formasPagarCredito);
    }

    public function eliminarGarante($customerId)
    {
        $this->garantesArreglo = array_filter($this->garantesArreglo, function ($item) use ($customerId) {
            return $item['customerId'] != $customerId;
        });
    }

    public function eliminarFormaPagarLiquidar($formaId, $formaName)
    {
        $this->formasPagarCreditoLiquidar = array_filter($this->formasPagarCreditoLiquidar, function ($item) use ($formaId, $formaName) {
            return $item['formaId'] != $formaId || $item['formaName'] != $formaName;
        });
    }

    public function generatePdfLiquidado($id)
    {
        $company = Company::find(Auth::user()->company_id);
        $credito = CreditFolderHeader::find($id);
        $customer = Customer::find($credito->customer_id);
        $letras = CreditFolderDetail::where('code_folder_header', $credito->code);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $data['company'] = $company;
        $data['customer'] = $customer;
        $data['credito'] = $credito;
        $data['letras'] = $letras->get();
        $data['totalCancelado'] = $letras->sum('valor_pagado');
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['totalCancelado'], 2, 'DÓLARES', 'CENTAVOS');

        $pdf = PDF::loadView('reportes.reciboCredito', compact('data'))
            ->setPaper('A6', "portrait");
        // Descargar el PDF
        return response()->streamDownload(
            function () use ($pdf) {
                print($pdf->output());
            },
            'Crédito' . $credito->code . '.pdf'
        );
    }

    public function borrarTodoCredito($id)
    {
        $credit =  CreditFolderHeader::find($id);
        $detalle = CreditFolderDetail::where('code_folder_header', $credit->code)->get();
        foreach ($detalle as $det) {
            $customerMovimiento = CustomerMovimiento::where('credit_folder_details_id', $det->id)->first();
            if ($customerMovimiento) {
                $historico = CustomerHistorial::where('customer_movimiento_code', $customerMovimiento->code)->first();
                if ($historico) {
                    $historico->delete();
                }
                $customerMovimiento->delete();
            }
            $det->delete();
        }
        $credit->delete();
    }
    public function customerSearchQuery()
    {
        return Customer::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where('nombres', 'like', $term)
                    ->orWhere('apellidos', 'like', $term)
                    ->orWhere('numero_documento', 'like', $term);
            })->orderBy('apellidos')->orderBy('nombres')->orderBy('id');
    }

    public function render()
    {
        $cajaAbierta = Cajas::where('status', 'ABIERTA')
            ->where('date_inicial', date('Y-m-d'))
            ->where('user_inicial_id', Auth::user()->id)
            ->count();
        if ($cajaAbierta == 1) {
            $this->verBotonlistaPrestamos = true;
        } else {
            $this->verBotonlistaPrestamos = false;
        }
        if ($this->id_seleccionado != 0) {
            $this->generarListadeCreditos();
        }
        if ($this->headerPago != 0) {
            $this->cargarDatosPrestamo($this->headerPago);
        }
        $clientes = $this->customerSearchQuery()->paginate(15);
        $cliente = Customer::find($this->id_seleccionado);
        $filesCreditos = CustomerFile::where('company_id', Auth::user()->company_id)->where('customer_id', $this->id_seleccionado)->where('credit_header_id', $this->id_credito)->get();
        $formasPago = FormasPago::where('status', 1)->get();
        $company = Company::find(Auth::user()->company_id);
        $prestamosListado = Prestamos::where('periodo_id', '!=', null)
            ->where('status', 'A')
            ->get();
        $garantes = Customer::orderBy('apellidos', 'asc')->get();
        $bancos = Bancos::where('tipo_cuenta_id', '>', 0)->where('numero_cuenta', '!=', '')->where('status', true)->get();
        return view('livewire.creditos.creditos-componet', compact('clientes', 'cliente', 'filesCreditos', 'formasPago', 'company', 'prestamosListado', 'garantes', 'bancos'));
    }
}
