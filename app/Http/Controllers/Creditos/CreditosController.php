<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Prestamos;
use App\Models\Customer;
use App\Models\RecurrenciaPrestamos;
use App\Models\Cajas;
use App\Models\LogCron;
use App\Models\CreditFolderDetail;
use App\Models\Company;
use App\Models\CreditFolderHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditosController extends Controller
{

    public function index()
    {
        return view('creditos.index');
    }

    public function creditos($id)
    {

        return view('creditos.index')->with('id', $id);
    }

    public function pdfLetras($valor, $cuota, $fecha, $tipo, $prestamo, $genera, $customer, $codigo)
    {
        $carpeta = '';
        $texMarca = '';
        $ahorro = '';
        $cust = Customer::find($customer);
        $prestamoTipo = Prestamos::find($prestamo);
        if ($genera == 0) {
            $texMarca = 'SIMULADOR';
        } else {
            $carpeta = 'EL NUMERO DE PRESTAMO ES: ' . $codigo;
            $creditoCabecer = CreditFolderHeader::where('code', $codigo)->first();
            if ($creditoCabecer) {
                $sumatoriaLetraAhorro = $creditoCabecer->ahorro + $creditoCabecer->valor_cuota;
                $ahorro = ' El credito tiene un ahorro mensual de : ' . $creditoCabecer->ahorro . ' $ , este valor se debe sumar a la letra mensual de : ' . $creditoCabecer->valor_cuota . ' $, siendo un total de: <label style="color:red;">' . $sumatoriaLetraAhorro . '$ </label>';
            }
        }
        $prestamos = Prestamos::find($prestamo);
        if ($prestamos->calculo_simple) {
            $cuotas = CreditosController::generarCalculoSimple($valor, $cuota, $fecha, $tipo, $prestamo, $customer);
        } else {
            if ($prestamos->diario == 0) {
                $cuotas = CreditosController::generarNormal($valor, $cuota, $fecha, $tipo, $prestamo, $customer);
            } else {
                $cuotas = CreditosController::generarDiario($valor, $cuota, $fecha, $tipo, $prestamo, $customer);
            }
        }
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
        $data['prestamoTipo'] = $prestamoTipo;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['detalle'] = $cuotas;
        $data['tipoPrestamo'] = $prestamoTipo->tipo;
        $data['customer'] = $cust;
        $data['texMarca'] = $texMarca;
        $data['carpeta'] = $carpeta;
        $data['ahorro'] = $ahorro;
        return PDF::loadView('creditos.pdf_creditos_generar', compact('data'))
            ->download('Credito.pdf');
    }

    public static function generarNormal($valor, $cuota, $fecha, $tipo, $prestamo, $customer)
    {
        $prestamos = Prestamos::find($prestamo);
        $customer = Customer::find($customer);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $fecha;
        $interes = $prestamos->interes;
        $deuda = $valor;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        switch ($recuTiempo) {
            case 'D':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / $cuota;
                    $cuotas = ($deuda * $inte * (pow((1 + $inte), ($cuota)))) / ((pow((1 + $inte), ($cuota))) - 1);
                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $cuota), 2);
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);

                    for ($i = 1; $i <= $cuota; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addDays($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2);
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
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
                    $plazo = $cuota; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / $cuota; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;
                    for ($i = 1; $i <= $cuota; $i++) {
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
                            'deuda' => round($dineroCalculo, 2),
                            'cuotaPago' => round($valInteres + $amortizado, 2),
                            'interes' => round($valInteres, 2),
                            'amoritizado' => round($amortizado, 2),
                        ];
                    }
                }
                break;
            case 'S':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / $cuota;
                    $cuotas = ($deuda * $inte * (pow((1 + $inte), ($cuota)))) / ((pow((1 + $inte), ($cuota))) - 1);
                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $cuota), 2);
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);

                    for ($i = 1; $i <= $cuota; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addWeeks($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2);
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
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
                    $plazo = $cuota; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / $cuota; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;

                    for ($i = 1; $i <= $cuota; $i++) {
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
                            'deuda' => round($dineroCalculo, 2),
                            'cuotaPago' => round($valInteres + $amortizado, 2),
                            'interes' => round($valInteres, 2),
                            'amoritizado' => round($amortizado, 2),
                        ];
                    }
                }
                break;
            case 'M':
                if ($prestamos->tipo == 'F') {
                    $inte = ($interes / 100) / $cuota;
                    $cuotas = ($deuda * $inte * (pow((1 + $inte), ($cuota)))) / ((pow((1 + $inte), ($cuota))) - 1);
                    $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $cuota), 2);
                    $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);

                    for ($i = 1; $i <= $cuota; $i++) {
                        $fecha = \Carbon\Carbon::parse($fechaPrestamo);
                        $periodo = $recurrencia->separacion;
                        $fechaMasDias = $fecha->addMonths($periodo);
                        $fechaPrestamo = $fechaMasDias->toDateString();

                        $amortizado = $cuotas - ($deuda * $inte);
                        $interes = number_format($deuda * $inte, 2);
                        $sumaInteres = $sumaInteres + $interes;
                        $deuda = $deuda - ($cuotas - ($deuda * $inte));
                        $pagos[] = [
                            'cuotas' => $i,
                            'fechas' => $fechaPrestamo,
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
                    $plazo = $cuota; // número de meses del plazo
                    $interes_mensual = ($interes_anual / 100) / $cuota; // tasa de interés mensual
                    $cuota_fija = $prestamo / $plazo; // cuota fija mensual
                    $amortizado = $prestamo / $plazo; // cuota fija mensual
                    $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
                    $saldo_pendiente = $prestamo; // saldo pendiente inicial
                    $dineroCalculo = $prestamo;

                    for ($i = 1; $i <= $cuota; $i++) {
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
                            'deuda' => round($dineroCalculo, 2),
                            'cuotaPago' => round($valInteres + $amortizado, 2),
                            'interes' => round($valInteres, 2),
                            'amoritizado' => round($amortizado, 2),
                        ];
                    }
                }
                break;
        }
        return $pagos;
    }

    public static function generarDiario($valor, $cuota, $fecha, $tipo, $prestamo, $customer)
    {
        $prestamos = Prestamos::find($prestamo);
        $customer = Customer::find($customer);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $fecha;
        $fechaPrestamoDiarios = $fecha;
        $interes = $prestamos->interes;
        $deuda = $valor;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;
        $divider = 30;
        switch ($recuTiempo) {
            case 'D':
                if ($prestamos->tipo == 'F') {
                    $inteMensual = $deuda * ($interes / 100);
                    $interesAnual = $inteMensual / 12;
                    $resultDiv = $cuota / $divider;
                    $numeroMes = ceil($resultDiv);
                    $interesLetras = number_format($interesAnual * $numeroMes, 2); //interes 
                    $valorFinal = $deuda + $interesLetras; //este es el valor final para ser dividido
                    $interesPorLetra = number_format($interesLetras / $cuota, 2);
                    $cuotaPago = number_format($valorFinal / $cuota, 2); //valor de la cuota
                    $contador = 1;

                    while ($contador <= $cuota) {
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
                    $fechaMasDias = $fecha->addWeeks($cuota);
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
                    $fechaMasDias = $fecha->addMonths($cuota);
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
        return $pagos;
    }

    public function generarCalculoSimple($valor, $cuota, $fecha, $tipo, $prestamo, $customer)
    {

        $prestamos = Prestamos::find($prestamo);
        $customer = Customer::find($customer);
        $recurrencia = RecurrenciaPrestamos::find($prestamos->periodo_id);
        $recuTiempo = $recurrencia->code;
        $pagos = array();
        $fechaPrestamo = $fecha;
        $interes = $prestamos->interes;
        $deuda = $valor;
        $desgravament = $prestamos->fondo_desgravamen;
        $sumaInteres = 0;



        $letras = $cuota;
        $inte = (($deuda * ($interes / 100)) / 12);
        $valorCapAmortizado = ($deuda + ($inte *  $letras)) / $letras;

        $cuotas = (($deuda + ($inte *  $letras)) /  $letras)  - $inte;
        $fondoDesgravamen = 0;
        $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2, '.', '');

        for ($i = 1; $i <= $cuota; $i++) {
            $fecha = \Carbon\Carbon::parse($fechaPrestamo);
            $periodo = 1;
            $fechaMasDias = $fecha->addMonths($periodo);
            $fechaPrestamo = $fechaMasDias->toDateString();
            $amortizado = $valorCapAmortizado;
            $interes = number_format($deuda * $inte,  2, '.', '');
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
        return $pagos;
    }
    public static function generarDebitos($cron)
    {
        $detalle = CreditFolderDetail::select(
            'credit_folder_details.*',
            'credit_folder_headers.id as credit_folder_headers_id',
            DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as nombre_completo"),
            'customer.numero_documento',
            DB::raw("DATEDIFF(NOW(), credit_folder_details.date_vencimiento) as dias_mora"),
            'tipo_ahorros.name as tipo_ahorro_name',
            'tipo_ahorros.class as tipo_ahorro_class',
            'customer_tipo_ahorros.id as customer_tipo_ahorros_id',
        )
            ->leftjoin('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->leftjoin('customer', 'credit_folder_headers.customer_id', '=', 'customer.id')
            ->leftjoin('customer_tipo_ahorros', 'customer.id', '=', 'customer_tipo_ahorros.customer_id')
            ->leftjoin('tipo_ahorros', 'customer_tipo_ahorros.tipo_ahorros_id', '=', 'tipo_ahorros.id')
            ->where('credit_folder_details.company_id', 1)
            ->where('credit_folder_details.status', 'PENDIENTE')
            ->where('credit_folder_headers.status', 'ENTREGADO')
            ->where('credit_folder_details.date_vencimiento', '<=', date('Y-m-d'))
            ->where(function ($query) {
                $query->whereRaw("tipo_ahorros.descargo_creditos = true OR tipo_ahorros.descargo_creditos IS NULL");
            })
            ->orderBy('dias_mora', 'desc')
            ->groupBy('credit_folder_details.id')
            ->get();
        $contador = 0;
        foreach ($detalle as $value) {
            echo "credito ->  .:.:." . $value->code_folder_header;
            $calculoMora = 0;
            $saldo = \App\Http\Controllers\Base\BaseController::saldoCuentaCliente($value->customer_tipo_ahorros_id);
            if ($saldo > 0) {
                $calculoMora = \App\Http\Controllers\Base\BaseController::calculoInteresMoraLetraValorCrom($value->id);
                $letra = CreditFolderDetail::find($value->id);
                $valor = $letra->valor_cuota + $calculoMora;
                if ($saldo >= $valor) {
                    $resouestaDebito = \App\Http\Controllers\Base\BaseController::descargoAutomaticoCuentaCreditoCron($valor, $value->customer_tipo_ahorros_id);
                    $respuesta = \App\Http\Controllers\Base\BaseController::pagoAutomaticoLetraCreditoCron($valor, $value->id, $calculoMora);
                    $log = CreditosController::guardarLogLetrasPagadas($cron, $letra);
                    echo "Letra pagada  .:.:.";
                    $contador++;
                } else {
                    echo "No tiene saldo suficiente .:.:.";
                }
            } else {
                echo "No tiene saldo suficiente .:.:.";
            }
        }
        echo ".:. Se pagaron un total de " . $contador . ' letras .:.';
    }

    public static function guardarLogLetrasPagadas($cron, $letra)
    {
        $header = CreditFolderHeader::where('code', $letra->code_folder_header)->first();
        $data = [
            'company_id' => $header->company_id,
            'cron_lists_id' => $cron->id,
            'credit_header_id' => $header->id,
            'credit_detail_id' => $letra->id,
            'detalle' => 'SE DEBITA DE FORMA AUTOMATIZADA EL VALOR DE ' . $letra->valor_final . '$ DEL CREDITO A NOMBRE DEL SR@ ' . $header->customer_name . '; CREDITO #' . $header->code . '; LETRA #' . $letra->numero_cuota,
            'date_create' => date('Y-m-d'),
            'hour_create' => date('H:i:s'),
        ];
        LogCron::create($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
