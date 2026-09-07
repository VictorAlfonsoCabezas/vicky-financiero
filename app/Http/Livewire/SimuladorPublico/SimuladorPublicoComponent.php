<?php

namespace App\Http\Livewire\SimuladorPublico;

use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use Carbon\Carbon;
use Livewire\Component;

use Darryldecode\Cart\Cart;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class SimuladorPublicoComponent extends Component
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

    public function mount()
    {
     
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

        return view('livewire.simulador-publico.simulador-publico-component');
    }
}
