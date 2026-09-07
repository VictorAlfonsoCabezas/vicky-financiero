<?php

namespace App\Http\Livewire\CreditosAutomaticos;

use App\Http\Controllers\Base\BaseController;
use App\Models\Company;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CreditosAutomaticosComponent extends Component
{

    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['pagarnovacion'];

    public $search = '';
    public $customer_selec = 0;
    public $id_seleccionado = 0;
    public $nombres = '';
    public $apellidos = '';
    public $telefono = '';
    public $totalCreditos = '';
    public $interesSuma = '';
    public $cuotas_simulador = '';
    public $opcionesPrestamo = [];
    public $tipo_simulador = '';
    public $listaLetras = [];
    public $diarioLetras = false;
    public $codigoPrestamo = '';
    public $valorCuota = 0;
    public $valorDesgravament = 0;
    public $valor_simulador = '';
    public $fecha_prestamo = '';
    public $prestamo_simulador = '';
    public $customerID = '';
    public $garante_prestamo = '';
    public $listaCreditos = [];
    public $encaje_valor = 0;
    public $codigoSugerido = 0;
    public $valor_ahorrar_credito = 0;
    public $valorCreditoSumado = 0;


    public function seleccionarCliente($id)
    {
        $this->interesSuma = '';
        if ($id > 0) {
            $this->customer_selec = $id;
            $company = Company::find(Auth::user()->company_id);
            $customer = Customer::find($id);
            $creditos = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('customer_id', $id)
                ->get();
            $this->id_seleccionado = $id;
            $this->nombres = $customer->nombres;
            $this->apellidos = $customer->apellidos;
            $this->totalCreditos = $creditos->count();
            $this->telefono = $customer->telefono;
            $this->cuotas_simulador = '';
            $this->customerID = $customer->id;
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
    public function verificarCodigo()
    {
        $cadena = $this->codigoPrestamo;
        if (preg_match('/^\d+$/', $cadena)) {
            $numero = (int)$cadena;
            $codeString = BaseController::generarCodigoPrestamosautomatico($numero, 6);
            $existeCredito = CreditFolderHeader::where('code', $codeString)->count();
            if ($existeCredito  == 0) {
                $this->codigoPrestamo = $codeString;
            } else {
                $this->codigoPrestamo = '';
                $color = 'danger';
                $mensaje = 'Error: el código ' . $codeString . ' ya fue tomado.';
                $data = [
                    'titulo' => 'Notificación',
                    'color' => $color,
                    'mensaje' => $mensaje
                ];
                $this->dispatchBrowserEvent('alerta', $data);
                return;
            }
        } else {
            $this->codigoPrestamo = '';
            $color = 'danger';
            $mensaje = 'Error: La cadena contiene caracteres no válidos.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }
    }
    public function simular()
    {
        $this->validate(
            [
                'codigoPrestamo' => 'required',
                'valor_simulador' => 'required',
                'cuotas_simulador' => 'required',
                'fecha_prestamo' => 'required',
                'tipo_simulador' => 'required',
                'prestamo_simulador' => 'required',

            ],
            [
                'codigoPrestamo.required' => 'Necesita ingresar un código.',
                'valor_simulador.required' => 'Necesita ingresar un valor.',
                'cuotas_simulador.required' => 'Necesita ingresar el numero de cuotas.',
                'fecha_prestamo.required' => 'Necesita seleccionar una fecha para iniciar el prestamo.',
                'tipo_simulador.required' => 'Necesita seleccionar el tipo de prestamo.',
                'prestamo_simulador.required' => 'Necesita seleccionar el prestamo.',

            ]
        );

        $prestamos = Prestamos::find($this->prestamo_simulador);

        $this->valorCreditoSumado = 0.00;
        if ($prestamos->suma_valores_gastos_prestamo) {
            if ($prestamos->administrativo_porcentaje_valor == "VALOR") {
                $valorAdministrativo  =  $prestamos->gasto_administrativo;
            } else {
                $solicitadoPrestamo = $this->valor_simulador;
                $valorAdministrativo  =  $solicitadoPrestamo * ($prestamos->gasto_administrativo / 100);
            }

            $valoresGastosCalculo = BaseController::valoresGastosCalculo($prestamos, $this->valor_simulador);


            $this->valorCreditoSumado =  $valoresGastosCalculo['gasto1'] + $valoresGastosCalculo['gasto2'] + $valoresGastosCalculo['gasto3'];

            $color = 'info';
            $mensaje = 'Este crédito suma el valor de ' . $this->valorCreditoSumado . ' $ por gastos administrativos al prestamo ' . $this->valor_simulador . " $";
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
        }
        if ($prestamos->diario == 0) {
            $this->generarNormal();
        } else {
            $this->generarDiario();
            $this->diarioLetras = true;
        }

        $this->generarDatos($this->customerID);
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
                    $inte = (($interes / 100) / 151);

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
                    } else {
                        $cuotas = $deuda  / $this->cuotas_simulador;
                    }

                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2, '.', '');
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
                            'valMes' => round($valInteres + $amortizado, 2),
                            'desgravamen' => $fondo_desgravamen,
                            'deuda' =>  number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => round($valInteres + $amortizado, 2),
                            'interes' => round($valInteres, 2),
                            'amoritizado' => round($amortizado, 2),
                        ];
                    }
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / 36;

                    if ($inte > 0) {
                        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($this->cuotas_simulador)))) / ((pow((1 + $inte), ($this->cuotas_simulador))) - 1);
                    } else {
                        $cuotas = $deuda  / $this->cuotas_simulador;
                    }

                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2, '.', '');
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
                            'valMes' => round($valInteres + $amortizado, 2),
                            'desgravamen' => $fondo_desgravamen,
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => round($valInteres + $amortizado, 2),
                            'interes' => round($valInteres, 2),
                            'amoritizado' => round($amortizado, 2),
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
                        $cuotas = $deuda  / $this->cuotas_simulador;
                    }

                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $this->cuotas_simulador), 2, '.', '');
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
                            'valMes' => round($valInteres + $amortizado, 2),
                            'desgravamen' => $fondo_desgravamen,
                            'deuda' => number_format($dineroCalculo, 2, '.', ''),
                            'cuotaPago' => round($valInteres + $amortizado, 2),
                            'interes' => round($valInteres, 2),
                            'amoritizado' => round($amortizado, 2),
                        ];
                    }
                }
                break;
        }
        $this->interesSuma = $sumaInteres;
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
                    $interesLetras = number_format($interesAnual * $numeroMes, 2); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $this->cuotas_simulador, 2);
                    $cuotaPago = number_format($valorFinal / $this->cuotas_simulador, 2); //valor de la cuota
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
                    $interesLetras = number_format($interesAnual * $numeroMes, 2); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $recorrido, 2);
                    $cuotaPago = number_format($valorFinal / $recorrido, 2); //valor de la cuota
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
                    $interesLetras = number_format($interesAnual * $numeroMes, 2); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $recorrido, 2);
                    $cuotaPago = number_format($valorFinal / $recorrido, 2); //valor de la cuota
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

    public function crearPrestamo()
    {

        $this->validate(
            [
                'codigoPrestamo' => 'required',
                'valor_simulador' => 'required',
                'cuotas_simulador' => 'required',
                'fecha_prestamo' => 'required',
                'tipo_simulador' => 'required',
                'prestamo_simulador' => 'required',

            ],
            [
                'codigoPrestamo.required' => 'Necesita ingresar un código.',
                'valor_simulador.required' => 'Necesita ingresar un valor.',
                'cuotas_simulador.required' => 'Necesita ingresar el numero de cuotas.',
                'fecha_prestamo.required' => 'Necesita seleccionar una fecha para iniciar el prestamo.',
                'tipo_simulador.required' => 'Necesita seleccionar el tipo de prestamo.',
                'prestamo_simulador.required' => 'Necesita seleccionar el prestamo.',

            ]
        );
        if ($this->customerID  == '') {
            $color = 'danger';
            $mensaje = 'debe seleccionar un cliente para continuar.';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);
            return;
        }

        $customer = Customer::find($this->customerID);
        $prestamos = Prestamos::find($this->prestamo_simulador);
        $valoresGastosCalculo = BaseController::valoresGastosCalculo($prestamos, $this->valor_simulador);
        $valorAdministrativo  = 0;
        if ($prestamos->administrativo_porcentaje_valor == "VALOR") {
            $valorAdministrativo  =  $prestamos->gasto_administrativo;
        } else {
            $solicitadoPrestamo = $this->valor_simulador;
            $valorAdministrativo  =  $solicitadoPrestamo * ($prestamos->gasto_administrativo / 100);
        }


        $garanteDato = Customer::find($this->garante_prestamo);
        $tabla = 'credit_folder_headers';

        $dataHeader = [
            "company_id" => Auth::user()->company_id,
            "code" => $this->codigoPrestamo,
            "customer_id" => $customer->id,
            "customer_code" => $customer->code,
            "customer_ruc" => $customer->numero_documento,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_phone" => $customer->telefono,
            "customer_address" => $customer->direccion,
            "customer_email" => $customer->correo,
            "customer_garante_id" => (isset($garanteDato->id)) ? $garanteDato->id : 0,
            "customer_garante_name" => (isset($garanteDato->id)) ? $garanteDato->nombres . ' ' . $garanteDato->apellidos : '',
            "valor_solicitado" => $this->valor_simulador,
            "anios_pagar" => $this->cuotas_simulador / 12,
            "cuotas_pagar" => $this->cuotas_simulador,
            "valor_cuota" => $this->valorCuota,
            "valor_desgravamen" => $this->valorDesgravament,
            "date_created" => $this->fecha_prestamo,
            "hour_created" => date("H:i:s"),
            "user_created_id" => Auth::user()->id,
            "user_created_name" => Auth::user()->username,
            "status" => 'ENTREGADO',
            "encaje_valor" => $this->encaje_valor,


            "tipo_prestamo" => $prestamos->id,
            "administrativo_porcentaje_valor" => $prestamos->administrativo_porcentaje_valor,
            "gasto_administrativo" => $valorAdministrativo,

            "encaje" => $prestamos->encaje,
            "encaje_credito_cuenta" => ($prestamos->encaje == 1) ? $prestamos->encaje_credito_cuenta : null,
            "encaje_porcentaje_valor" => ($prestamos->encaje == 1) ? $prestamos->encaje_porcentaje_valor : null,
            "encaje_cantidad" => ($prestamos->encaje == 1) ? $prestamos->encaje_cantidad : 0,
            //"encaje_valor" => ($prestamos->encaje == 1) ? $this->encaje_valor : 0,

            "primer_gasto" => $valoresGastosCalculo['gasto1'],
            "segundo_gasto" => $valoresGastosCalculo['gasto2'],
            "tercer_gasto" => $valoresGastosCalculo['gasto3'],

            "porcentaje_primer_gasto" => $valoresGastosCalculo['porcentage1'],
            "porcentaje_segundo_gasto" => $valoresGastosCalculo['porcentage2'],
            "porcentaje_tercer_gasto" => $valoresGastosCalculo['porcentage3'],


            "ahorro" => $this->valor_ahorrar_credito,
        ];
        $createHeader = CreditFolderHeader::create($dataHeader);

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

        $this->codigoPrestamo = '';
        $this->valor_simulador = '';
        $this->cuotas_simulador = '';
        $this->fecha_prestamo = '';
        $this->tipo_simulador = '';
        $this->prestamo_simulador = '';
        $this->customerID  = '';
        $this->garante_prestamo  = '';
        $this->listaLetras  = [];
        $this->encaje_valor  = 0;

        $color = 'success';
        $mensaje = 'Creado correctamente.';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        return;
    }

    public function render()
    {

        $tabla = 'credit_folder_headers';
        $codeString = BaseController::generarCodigoPrestamos($tabla, 6);
        $this->codigoSugerido = $codeString;
        $garantes = Customer::all();

        $clientes = Customer::where('company_id', Auth::user()->company_id)
            ->where('nombres', 'like', '%' . $this->search . '%')
            ->orWhere('apellidos', 'like', '%' . $this->search . '%')
            ->orWhere('numero_documento', 'like', '%' . $this->search . '%')
            ->paginate(15);


        return view('livewire.creditos-automaticos.creditos-automaticos-component', compact('clientes', 'garantes'));
    }
}
