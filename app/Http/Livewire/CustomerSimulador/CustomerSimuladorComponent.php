<?php

namespace App\Http\Livewire\CustomerSimulador;

use App\Models\Customer;
use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Component;


use App\Models\Company;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\CustomerMovimiento;
use App\Http\Controllers\Base\BaseController;
use App\Models\FormasPago;
use App\Models\Garantes;
use App\User;
use Illuminate\Support\Facades\Auth;
use PDF;
use Luecano\NumeroALetras\NumeroALetras;



class CustomerSimuladorComponent extends Component
{

    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $opcionesPrestamo = [];
    public $valor_simulador = '';
    public $cuotas_simulador = '';
    public $fecha_prestamo = '';
    public $tipo_simulador = '';
    public $prestamo_simulador = '';
    public $valor_ahorrar_credito = '';
    public $diarioLetras = false;
    public $valorCuota = '';
    public $valorDesgravament = '';
    public $interesSuma = '';
    public $listaLetras = [];
    public $valorCreditoSumado = 0;

    
    public $verTabla = '';
    public $verPdf = false;
    public $mensajeCreacion = '1';
    public $codigoPrestamo = '';
    public $procesando = false;
    public $generar = '';
    public $customerID = '';
    public $estadoCreditoNovacion = false;
    public $verBtnGenerar = true;

    public $id_seleccionado = 0;
    public $listaCreditos = [];
    public $nombres = '';
    public $apellidos = '';
    public $telefono = '';
    public $totalCreditos = '';
    public $cabeceraCreada = '';
    public $garantesArreglo = [];
    public $pararTodo = 0;
    public $codeString = '';
    public $customer_selec = 0;
    public $fechaPagoLetra = '';
    public $companyPagare = false;
    public $companyLetraCambio = false;
    public $obligarGarante = '';


    public function generarPdfLetrasCreditos()
    {
        $company = Company::find(Auth::user()->company_id);
        //dd("entra");
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

        $data['prestamo'] = Prestamos::find($this->prestamo_simulador);
        $data['valorAdministrativo']  = 0;
        $data['solicitadoPrestamo'] = $this->valor_simulador;

        if ($data['prestamo']->administrativo_porcentaje_valor == "VALOR") {
            $data['valorAdministrativo']  =  $data['prestamo']->gasto_administrativo;
        } else {
            $solicitadoPrestamo = $this->valor_simulador;
            $data['valorAdministrativo']  =  $solicitadoPrestamo * ($data['prestamo']->gasto_administrativo / 100);
        }

        $data['anios_pagar'] = $this->cuotas_simulador / 12;
        $data['cuotas_pagar'] = $this->cuotas_simulador;
        $data['valor_cuota'] = $this->valorCuota;
        $valoresGastosCalculo = BaseController::valoresGastosCalculo($data['prestamo'], $this->valor_simulador);
        $data['valorCreditoSumado'] =  $valoresGastosCalculo['gasto1'] + $valoresGastosCalculo['gasto2'] + $valoresGastosCalculo['gasto3'];
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
        }, 'SIMULACION DE PRESTAMO.pdf');
    }

    public function seleccionarCliente($id)
    {
        $this->listaLetras = [];
        $this->interesSuma = '';
        if ($id > 0) {
            $this->customer_selec = $id;
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
        } else {
            $this->id_seleccionado = 0;
        }
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
        $prestamo =  Prestamos::find($this->prestamo_simulador);
        $this->valor_ahorrar_credito = $prestamo->ahorro;
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
                
            ],
            [
                'valor_simulador.required' => 'Necesita ingresar un valor.',
                'cuotas_simulador.required' => 'Necesita ingresar el numero de cuotas.',
                'tipo_simulador.required' => 'Necesita seleccionar una tabla.',
                'fecha_prestamo.required' => 'Necesita seleccionar una fecha para iniciar el prestamo.',
                'prestamo_simulador.required' => 'Necesita seleccionar el prestamo que desea realizar.',
               
            ]
        );

        $prestamos = Prestamos::find($this->prestamo_simulador);
       //dd('ss');
       

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
    public function generarSimple()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $pagos = array();
        $fechaPrestamo = $this->fecha_prestamo;
        $interes = $prestamos->interes;
        $deuda = $this->valor_simulador + $this->valorCreditoSumado;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        $letras = $this->cuotas_simulador;
        $inte = (($deuda * ($interes / 100)) / 12);
        $valorCapAmortizado = ($deuda + ($inte *  $letras)) / $letras;

        $cuotas = (($deuda + ($inte *  $letras)) /  $letras)  - $inte;
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
            $interes = number_format($inte,  2, '.', '');
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

    public function generarNormal()
    {
        $prestamos = Prestamos::find($this->prestamo_simulador);
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
                    $inte = (($interes / 100) / 151);
                    $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
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
                            $interes = number_format(($deuda * $inte),  2, '.', '');                       
                            $sumaInteres = $sumaInteres + $interes;
                            $deuda = $deuda - ($cuotas - ($deuda * $inte));
                            $pagos[] = [
                                'cuotas' => $i,
                                'fechas' => $fechaPrestamo,
                                'valMes' => $valorCuotas,
                                'desgravamen' => $fondoDesgravamen,
                                'deuda' =>  number_format($deuda, 2, '.', ''),
                                'cuotaPago' => $valorCuotas,
                                'interes' => $interes,                              
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
                            'deuda' =>  number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => number_format($valInteres + $amortizado, 2, '.', ''),
                            'interes' => number_format($valInteres, 2, '.', ''),
                            'amoritizado' => number_format($amortizado, 2, '.', ''),
                        ];
                    }
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {                    
                    $inte = ($interes / 100) / 36;
                    $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
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
                        $interes = number_format(($deuda * $inte) / 4,  2, '.', '');
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
                    $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
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
                        $interes = number_format($deuda * $inte,  2, '.', '');
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

    public function render()
    {
       
        return view('livewire.customer-simulador.customer-simulador-component');
    }
}
