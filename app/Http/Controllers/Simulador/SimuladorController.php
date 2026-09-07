<?php

namespace App\Http\Controllers\Simulador;

use App\Http\Controllers\Controller;
use App\Models\CustomerTarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditFolderDetail;
use App\Models\RecurrenciaPrestamos;
use App\Models\Company;
use App\Models\Prestamos;
use Response;
use PDF;

class SimuladorController extends Controller {

    public function index() {
        $tarifas = CustomerTarifa::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $prestamos = Prestamos::where('company_id', Auth::user()->company_id)->where('status', 'A')->get();
        $recurrencia = RecurrenciaPrestamos::where('company_id', Auth::user()->company_id)->get();
        return view('simulador/index')
                        ->with('recurrencia', $recurrencia)
                        ->with('prestamos', $prestamos)
                        ->with('tarifas', $tarifas);
    }

    public function simular(Request $request) {
        $prestamoTipo = Prestamos::find($request->input('prestamo_select'));
        $company = Company::find(Auth::user()->company_id);
        $sumaInteres = 0;
        $deuda = $request->input('valor_prestamo'); // cantidad del préstamo
        $anos = $request->input('plazo_anual');
        $mes = $request->input('cuotas_mensuales');
        $interes = $prestamoTipo->interes; // tasa de interés anual
        $desgravament = $prestamoTipo->fondo_desgravamen;
        $numcupotas = ($anos * 12) + ($mes); // número de meses del plazo
        $valorCuotas = 0;
        $pagos = array();
        if ($prestamoTipo->tipo == 'F') {
            $inte = ($interes / 100) / $numcupotas;
            $cuotas = ($deuda * $inte * (pow((1 + $inte), (($anos * 12) + $mes)))) / ((pow((1 + $inte), (($anos * 12) + $mes))) - 1);
            $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $numcupotas), 2);
            $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);
            $fechaActual = date('Y-m-d');
            for ($i = 1; $i <= $numcupotas; $i++) {
                $amortizado = $cuotas - ($deuda * $inte);
                $interes = number_format($deuda * $inte, 2);
                $sumaInteres = $sumaInteres + $interes;
                $deuda = $deuda - ($cuotas - ($deuda * $inte));
                $pagos [] = [
                    'cuotas' => $i,
                    'fechas' => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                    'valMes' => $valorCuotas,
                    'desgravamen' => $fondoDesgravamen,
                    'deuda' => number_format($deuda, 2),
                    'cuotaPago' => number_format($valorCuotas, 2),
                    'interes' => $interes,
                    'amoritizado' => number_format($amortizado, 2),
                ];
            }
        } else {
            $prestamo = $deuda; // cantidad del préstamo
            $interes_anual = $interes; // tasa de interés anual
            $plazo = $numcupotas; // número de meses del plazo
            $interes_mensual = $interes_anual / 100; // tasa de interés mensual
            $cuota_fija = $prestamo / $plazo; // cuota fija mensual
            $amortizado = $prestamo / $plazo; // cuota fija mensual
            $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
            $saldo_pendiente = $prestamo; // saldo pendiente inicial
            $fechaActual = date('Y-m-d');

            $dineroCalculo = $prestamo;
            $dinero = $prestamo;
            for ($i = 1; $i <= $plazo; $i++) {
                $valInteres = $dineroCalculo * $interes_mensual;
                $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                $cuota_total = $cuota_fija + ($fondo_desgravamen / $plazo); // cuota total (cuota fija + parte del fondo de desgravamen) 
                $sumaInteres = $sumaInteres + $valInteres;
                $dineroCalculo = $dineroCalculo - $amortizado;

                $pagos[] = array(
                    'mes' => $i,
                    'cuotas' => $i,
                    'fechas' => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                    'valMes' => round($valInteres + $amortizado, 2),
                    'amoritizado' => round($amortizado, 2),
                    'interes' => round($valInteres, 2),
                    'saldo_pendiente' => round($saldo_pendiente, 2),
                    'desgravamen' => 0,
                    'cuotaPago' => round($valInteres + $amortizado, 2),
                    'deuda' => round($dineroCalculo, 2),
                );
            }
        }
        $data = [
            'valorCuota' => $valorCuotas,
            'nueroCUptas' => $numcupotas,
            'pagos' => $pagos,
            'interesSuma' => number_format($sumaInteres, 2),
            'tipo' => $prestamoTipo->tipo,
        ];
        return Response::json($data);
    }

    public function pdfLetras($valor, $anual, $mensual, $id) {
        $prestamoTipo = Prestamos::find($id);
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
        $sumaInteres = 0;
        $deuda = $valor; // cantidad del préstamo
        $anos = $anual;
        $mes = $mensual;
        $interes = $prestamoTipo->interes; // tasa de interés anual
        $desgravament = $prestamoTipo->fondo_desgravamen;
        $numcupotas = ($anos * 12) + ($mes); // número de meses del plazo
        $valorCuotas = 0;
        $pagos = array();
        if ($prestamoTipo->tipo == 'F') {
            $inte = ($interes / 100) / $numcupotas;
            $cuotas = ($deuda * $inte * (pow((1 + $inte), (($anos * 12) + $mes)))) / ((pow((1 + $inte), (($anos * 12) + $mes))) - 1);
            $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $numcupotas), 2);
            $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);
            $fechaActual = date('Y-m-d');
            for ($i = 1; $i <= $numcupotas; $i++) {
                $amortizado = $cuotas - ($deuda * $inte);
                $interes = number_format($deuda * $inte, 2);
                $sumaInteres = $sumaInteres + $interes;
                $deuda = $deuda - ($cuotas - ($deuda * $inte));
                $pagos [] = [
                    'cuotas' => $i,
                    'fechas' => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                    'valMes' => $valorCuotas,
                    'desgravamen' => $fondoDesgravamen,
                    'deuda' => number_format($deuda, 2),
                    'cuotaPago' => number_format($valorCuotas, 2),
                    'interes' => $interes,
                    'amoritizado' => number_format($amortizado, 2),
                ];
            }
        } else {
            $pagos = array(); // arreglo para almacenar los datos de la tabla de amortización
            $prestamo = $deuda; // cantidad del préstamo
            $interes_anual = $interes; // tasa de interés anual
            $plazo = $numcupotas; // número de meses del plazo
            $interes_mensual = $interes_anual / 100; // tasa de interés mensual
            $cuota_fija = $prestamo / $plazo; // cuota fija mensual
            $amortizado = $prestamo / $plazo; // cuota fija mensual
            $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
            $saldo_pendiente = $prestamo; // saldo pendiente inicial
            $fechaActual = date('Y-m-d');

            $dineroCalculo = $prestamo;
            $dinero = $prestamo;
            for ($i = 1; $i <= $plazo; $i++) {
                $valInteres = $dineroCalculo * $interes_mensual;
                $interes_mes = $saldo_pendiente * $interes_mensual; // interés a pagar en el mes
                $amortizacion = $cuota_fija - $interes_mes; // parte de la cuota que se destina a amortización
                $saldo_pendiente -= $amortizacion; // saldo pendiente después de la amortización
                $cuota_total = $cuota_fija + ($fondo_desgravamen / $plazo); // cuota total (cuota fija + parte del fondo de desgravamen) 
                $sumaInteres = $sumaInteres + $valInteres;
                $dineroCalculo = $dineroCalculo - $amortizado;

                $pagos[] = array(
                    'mes' => $i,
                    'cuotas' => $i,
                    'fechas' => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                    'valMes' => round($valInteres + $amortizado, 2),
                    'amoritizado' => round($amortizado, 2),
                    'interes' => round($valInteres, 2),
                    'saldo_pendiente' => round($saldo_pendiente, 2),
                    'desgravamen' => 0,
                    'cuotaPago' => round($valInteres + $amortizado, 2),
                    'deuda' => round($dineroCalculo, 2),
                );
            }
        }
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['detalle'] = $pagos;
        $data['tipoPrestamo'] = $prestamoTipo->tipo;
        return PDF::loadView('simulador.pdf_simulador', compact('data'))
                        ->download('Simulador de Credito.pdf');
    }

}
