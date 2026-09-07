<?php

namespace App\Http\Controllers\Credit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Cajas\CajasController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\CustomerHistorial;
use App\Models\FondoHeader;
use App\Http\Controllers\Fondo\FondoController;
use App\Models\CustomerParentezco;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\CustomerTarifa;
use App\Models\Customer;
use App\Models\TypeTransaction;
use App\Models\CustomerMovimiento;
use App\Models\CreditFiles;
use App\Models\Company;
use App\Models\RecurrenciaPrestamos;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Luecano\NumeroALetras\NumeroALetras;
use Response;
use PDF;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CreditosPagados;
use App\Exports\CreditoVencidos;
use App\Exports\CreditPendientes;
use App\Models\DocumentosParametrizables;
use App\Models\Garantes;
use App\Models\Prestamos;
use DateTime;

class CreditController extends Controller
{

    public function index()
    {
        $creditos = CreditFolderHeader::where('company_id', Auth::user()->company_id)->groupBy('customer_id')->get();
        foreach ($creditos as $value) {
            $value->totalprestamo = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('customer_id', $value->customer_id)->where('status', '!=', 'NEGADO')->sum('valor_solicitado');
            $value->totalpagando = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('customer_id', $value->customer_id)->where('status', '!=', 'NEGADO')->sum('total_pagando');
        }
        $prestamos = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('status', '!=', 'NEGADO')->sum('valor_solicitado');
        $deudas = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('status', '!=', 'NEGADO')->sum('total_pagando');
        return view('credit/index')
            ->with('prestamos', $prestamos)
            ->with('deudas', $deudas)
            ->with('creditos', $creditos);
    }

    public function create()
    {
        $company = Company::find(Auth::user()->company_id);
        $garantes = Customer::where('customer_tarifa_name', 'SOCIO')->get();
        $tarifas = CustomerTarifa::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        $prestamos = Prestamos::where('company_id', Auth::user()->company_id)->where('status', 'A')->get();
        $recurrencia = RecurrenciaPrestamos::where('company_id', Auth::user()->company_id)->get();
        return view('credit/create')
            ->with('recurrencia', $recurrencia)
            ->with('company', $company)
            ->with('prestamos', $prestamos)
            ->with('garantes', $garantes)
            ->with('tarifas', $tarifas);
    }

    public function prestamos($id)
    {
        $company = Company::find(Auth::user()->company_id);
        $customer = Customer::find($id);
        $creditos = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $id)
            ->get();
        foreach ($creditos as $value) {
            $value->totalLetrasImpagas = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->count();
            $value->valorDeve = CreditFolderDetail::where('code_folder_header', $value->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            if ($value->tipo_prestamo != null && $value->tipo_prestamo != '') {
                $prestamoTipo = Prestamos::find($value->tipo_prestamo);
                $value->retener = $prestamoTipo->fondo_desgravamen;
            } else {
                $value->retener = $company->porcentaje_retener_credito;
            }
        }
        $valorPrestamos = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $id)
            ->sum('valor_solicitado');
        $valorDeudas = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $id)
            ->sum('total_pagando');
        CreditController::cuotasInteresMora($id);
        return view('credit/prestamos')
            ->with('company', $company)
            ->with('customer', $customer)
            ->with('valorPrestamos', $valorPrestamos)
            ->with('valorDeudas', $valorDeudas)
            ->with('creditos', $creditos);
    }

    public static function cuotasInteresMora($customer)
    {
        $cabecera = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $customer)
            ->where('status', 'ENTREGADO');
        if ($cabecera->count() > 0) {
            foreach ($cabecera->get() as $value) {
                $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
                    ->where('code_folder_header', $value->code)
                    ->where('status', 'PENDIENTE')
                    ->where('date_vencimiento', '<=', date('Y-m-d'));
                if ($detalle->count() > 0) {
                    foreach ($detalle->get() as $detail) {
                        $company = Company::find(Auth::user()->company_id);
                        $dias = $company->dias_mora;
                        $date1 = Carbon::createFromFormat('Y-m-d', $detail->date_vencimiento);
                        $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $dias_mora = $date1->diffInDays($date2);
                        if ($dias_mora >= $dias) {
                            $valor = $company->porcentaje_mora;
                            $interval = $date2->diff($date1);
                            $intervalMeses = (int) $interval->format("%m");
                            $porcentaje = (float) round(($detail->valor_cuota * $intervalMeses * ($valor / 100)), 2);
                            $detalleUpdate = CreditFolderDetail::find($detail->id);
                            $detalleUpdate->interes_mora = $porcentaje;
                            $detalleUpdate->save();
                        }
                    }
                }
            }
        }
    }

    public function verPagos($id)
    {
        CreditController::generarInteresMora($id);
        $header = CreditFolderHeader::find($id);
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $header->code)->get();
        foreach ($detalle as $value) {
            $value->pago_general = (float) round(($value->valor_cuota + $value->faltante_anterior_cuota + $value->interes_mora - $value->saldo_anterior_cuota), 2);
        }
        return Response::json($detalle);
    }

    public static function generarInteresMora($id)
    {
        $header = CreditFolderHeader::find($id);
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->where('date_vencimiento', '<', date('Y-m-d'))
            ->get();
        $company = Company::find(Auth::user()->company_id);
        foreach ($vencidas as $key => $value) {
            //            fecha de pago
            $fechaPagoInicial = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $fechaPago = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            //            fechas mas dias de plazo
            $fechaProrroga = $fechaPagoInicial->addDays($company->dias_inicio_cobro);
            $fechaProrrogaTrabajo = $fechaPago->addDays($company->dias_inicio_cobro);
            //            fecha actual
            $fechaActual = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $fechaActualTrabajo = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            //            calculamos la diferencia de dias entre la fecha actual y la fecha de prorroga
            $diasDebeFechaActual = $fechaProrrogaTrabajo->diffInDays($fechaActualTrabajo);
            if ($company->select_tipo_interes == 'D') {
                //                dias maximo que debe generar mora
                $diasTope = $company->numero_dias_interes;
                $diasReales = 0;
                if ($diasDebeFechaActual > 0) {
                    $diasReales = $diasDebeFechaActual + $company->dias_inicio_cobro - $company->dias_gracia;
                }
                if ($diasReales > $diasTope) {
                    $diasAdd = $diasTope - $company->dias_gracia;
                } else {
                    $diasAdd = $diasReales;
                }
            } else if ($company->select_tipo_interes == 'M') {
                // Obtener el último día del mes de la fecha de pago
                $diasTope = $fechaProrrogaTrabajo->endOfMonth();
                //                ver dias
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

    public function generarCredito(Request $request)
    {
        $prestamoTipo = Prestamos::find($request->input('prestamo_select'));
        $company = Company::find(Auth::user()->company_id);
        $sumaInteres = 0;
        $deuda = $request->input('valor_prestamo'); // cantidad del préstamo
        $anos = $request->input('plazo_anual');
        $mes = $request->input('cuotas_mensuales');
        $interes = $prestamoTipo->interes; // tasa de interés anual
        $desgravament = $prestamoTipo->fondo_desgravamen;
        $numCuotas = ($anos * 12) + ($mes); // número de meses del plazo
        $valorCuotas = 0;
        $customer = Customer::find($request->input('id_customer_generar'));
        $garante = Customer::find($request->input('garante'));
        if ($prestamoTipo->tipo == 'F') {
            $inte = ($interes / 100) / $numCuotas;
            $cuotas = ($deuda * $inte * (pow((1 + $inte), (($anos * 12) + $mes)))) / ((pow((1 + $inte), (($anos * 12) + $mes))) - 1);
            $fondoDesgravamen = number_format((($deuda * ($desgravament / 100)) / $numCuotas), 2);
            $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);
            $fechaActual = $request->input('date_created');
            $tabla = 'credit_folder_headers';
            $code = BaseController::generarCodigo($tabla, 3);
            $dataHeader = [
                "company_id" => Auth::user()->company_id,
                "code" => $code,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "customer_ruc" => $customer->numero_documento,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_phone" => $customer->telefono,
                "customer_address" => $customer->direccion,
                "customer_email" => $customer->correo,
                "customer_garante_id" => ($garante != null) ? $garante->id : 0,
                "customer_garante_name" => ($garante != null) ? $garante->nombres . '' . $garante->apellidos : null,
                "valor_solicitado" => $deuda,
                "anios_pagar" => $request->input('plazo_anual'),
                "cuotas_pagar" => $numCuotas,
                "valor_cuota" => $valorCuotas,
                "valor_desgravamen" => $fondoDesgravamen,
                "date_created" => $request->input('date_created'),
                "hour_created" => date("H:i:s"),
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "tipo_prestamo" => $prestamoTipo->id,
            ];
            $createHeader = CreditFolderHeader::create($dataHeader);
            for ($i = 1; $i <= $numCuotas; $i++) {
                $amortizado = $cuotas - ($deuda * $inte);
                $interes = round($deuda * $inte, 2);
                $sumaInteres = $sumaInteres + $interes;
                $deuda = $deuda - ($cuotas - ($deuda * $inte));
                $dataDetalle = [
                    "numero_cuota" => $i,
                    "company_id" => Auth::user()->company_id,
                    "code_folder_header" => $createHeader->code,
                    "date_created" => $request->input('date_created'),
                    "hour_created" => date("H:i:s"),
                    "date_vencimiento" => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                    "interes_periodo" => round($interes, $company->numero_decimales),
                    "capital_amortizado" => round($amortizado, $company->numero_decimales),
                    "fondo_desgravamen" => round($fondoDesgravamen, $company->numero_decimales),
                    "valor_cuota" => round($valorCuotas, $company->numero_decimales),
                    "saldo_remanente" => round($deuda, $company->numero_decimales),
                ];
                $createDetalle = CreditFolderDetail::create($dataDetalle);
            }
            $createHeader->valor_interes_pago = $sumaInteres;
            $createHeader->save();
        } else {
            $prestamo = $deuda; // cantidad del préstamo
            $interes_anual = $interes; // tasa de interés anual
            $plazo = $numCuotas; // número de meses del plazo
            $interes_mensual = $interes_anual / 100; // tasa de interés mensual
            $cuota_fija = $prestamo / $plazo; // cuota fija mensual
            $amortizado = $prestamo / $plazo; // cuota fija mensual
            $fondo_desgravamen = $prestamo * $desgravament; // monto del fondo de desgravamen (0.1% del monto del préstamo)
            $saldo_pendiente = $prestamo; // saldo pendiente inicial
            $fechaActual = $request->input('date_created');
            $tabla = 'credit_folder_headers';
            $code = BaseController::generarCodigo($tabla, 3);
            $dataHeader = [
                "company_id" => Auth::user()->company_id,
                "code" => $code,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "customer_ruc" => $customer->numero_documento,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_phone" => $customer->telefono,
                "customer_address" => $customer->direccion,
                "customer_email" => $customer->correo,
                "customer_garante_id" => ($garante != null) ? $garante->id : 0,
                "customer_garante_name" => ($garante != null) ? $garante->nombres . '' . $garante->apellidos : null,
                "valor_solicitado" => $deuda,
                "anios_pagar" => $request->input('plazo_anual'),
                "cuotas_pagar" => $numCuotas,
                "valor_cuota" => round($cuota_fija, 2),
                "valor_desgravamen" => $fondo_desgravamen,
                "date_created" => $request->input('date_created'),
                "hour_created" => date("H:i:s"),
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "tipo_prestamo" => $prestamoTipo->id,
            ];
            $createHeader = CreditFolderHeader::create($dataHeader);
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
                $dataDetalle = [
                    "numero_cuota" => $i,
                    "company_id" => Auth::user()->company_id,
                    "code_folder_header" => $createHeader->code,
                    "date_created" => $request->input('date_created'),
                    "hour_created" => date("H:i:s"),
                    "date_vencimiento" => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                    "interes_periodo" => $valInteres,
                    "capital_amortizado" => round($amortizado, 2),
                    "fondo_desgravamen" => 0,
                    "valor_cuota" => round($valInteres + $amortizado, 2),
                    "saldo_remanente" => round($dineroCalculo, 2),
                ];
                $createDetalle = CreditFolderDetail::create($dataDetalle);
                $createHeader->valor_interes_pago = $sumaInteres;
                $createHeader->save();
            }
        }
        $cabeceraArmar = CreditFolderHeader::find($createHeader->id);
        $detalleArmar = CreditFolderDetail::where('code_folder_header', $cabeceraArmar->code)->get();
        $data = [
            'cabecera' => $cabeceraArmar,
            'detalle' => $detalleArmar,
            'prestamo' => $prestamoTipo,
        ];
        return Response::json($data);
    }

    public function printDetalle($id)
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
        $path2 = 'codev/negro.png';
        $man = file_get_contents(public_path($path));
        $data['company'] = $company;
        $data['imagen'] = base64_encode($man);
        $man2 = file_get_contents(public_path($path2));
        $data['imagen2'] = base64_encode($man2);
        $data['cabecera'] = CreditFolderHeader::find($id);
        $data['detalle'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->get();
        $data['customer'] = Customer::find($data['cabecera']->customer_id);
        if ($data['customer']->estado_civil == 'CASADO/A' || $data['customer']->estado_civil == 'UNION LIBRE') {
            $civil = 'casado';
        } else {
            $civil = 'soltero';
        }
        $data['estado_civil'] = $civil;
        $data['tarifa_name'] = $data['customer']->customer_tarifa_name;
        $data['numero_garante'] = $data['cabecera']->customer_garante_id;
        $data['garante'] = ($data['cabecera']->customer_garante_id != 0) ? Customer::find($data['cabecera']->customer_garante_id) : '';
        return PDF::loadView('reportes.credito', compact('data'))
            ->download('Credito de : ' . $data['cabecera']->customer_name . '.pdf');
    }

    public function printDetalleCredito($id)
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
        $path2 = 'codev/negro.png';
        $man = file_get_contents(public_path($path));
        $data['company'] = $company;
        $data['imagen'] = base64_encode($man);
        $man2 = file_get_contents(public_path($path2));
        $data['imagen2'] = base64_encode($man2);
        $data['cabecera'] = CreditFolderHeader::find($id);
        $data['usario_creador'] = User::find($data['cabecera']->user_created_id);
        $data['tea_valor'] = "";
        $prestamos = Prestamos::find($data['cabecera']->tipo_prestamo);
        if ($prestamos){
            $data['tea_valor'] = $prestamos->interes_anual;
        }
        $data['nombrePrestamo'] = "";
        if($prestamos){
            $data['nombrePrestamo'] = $prestamos->name;
        }
        $data['valorDeve']  = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
        $ultimo = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->orderBy('id', 'desc')->first();
        $data['ultimo'] = $ultimo->date_vencimiento;
        
        $data['tipo_pago'] = "";
        $prestamos_tipo = CreditFolderHeader::where('company_id', $data['cabecera']->company_id)->orderBy('id', 'desc')->first();
        if($prestamos_tipo){
            $data['tipo_pago'] = $prestamos_tipo->tipo_pago;
        }
        $data['detalle'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->get();
        $data['primeraLetra'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->first();
        $data['detallePrimera'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->first();
        $data['detalleSumaInteres'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->sum('interes_periodo');
        $data['customer'] = Customer::find($data['cabecera']->customer_id);
        if ($data['customer']->estado_civil == 'CASADO/A' || $data['customer']->estado_civil == 'UNION LIBRE') {
            $civil = 'casado';
        } else {
            $civil = 'soltero';
        }
        $data['estado_civil'] = $civil;
        $data['tarifa_name'] = $data['customer']->customer_tarifa_name;
        $data['numero_garante'] = $data['cabecera']->customer_garante_id;
        $data['garante'] = ($data['cabecera']->customer_garante_id != 0) ? CustomerParentezco::find($data['cabecera']->customer_garante_id) : '';
        $data['garanteMostrar'] = Customer::find($data['cabecera']->customer_garante_id);
        $data['garantesTabla'] = Garantes::where('credit_folder_headers_id', $id)->get();


        return PDF::loadView('reportes.creditoDetalle', compact('data'))
            ->download('Credito de : ' . $data['cabecera']->customer_name . '.pdf');
    }

    public function printLetra($id)
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
        $data['cabecera'] = CreditFolderHeader::find($id);
        $data['ultimo'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->orderBy('id', 'desc')->first();
        $fechaUltimaLetra = $data['ultimo']->date_vencimiento;
        $data['primero'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->orderBy('id', 'ASC')->first();
        $prestamo = Prestamos::find($data['cabecera']->tipo_prestamo);
        $data['interesCredito'] = (isset($prestamo->interes)) ? $prestamo->interes : '';
        $data['dias'] = ($data['cabecera']->cuotas_pagar) * (30);
        $data['customer'] = Customer::find($data['cabecera']->customer_id);


        $date = new DateTime($fechaUltimaLetra);
        $dia = $date->format('d');
        $mes = $date->format('m');
        $año = $date->format('Y');
        $meses = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre'
        ];
        $fecha_formateada = sprintf('%02d de %s del %d', $dia, $meses[$mes], $año);
        $data['fechaFormateadaFinal'] =  $fecha_formateada;

        return PDF::loadView('reportes.letra', compact('data'))
            ->download('Letra de : ' . $data['cabecera']->customer_name . '.pdf');
    }

    public function printPagare($id)
    {
        CreditFolderHeader::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }

        $data['textoPlazo']  = "El pago se realizará cada 30 días";

        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);

        $DocumentoPersonalizado = DocumentosParametrizables::where('company_id', Auth::user()->company_id)->where('formato', 'PAGARE')->first();
        if ($DocumentoPersonalizado) {
            $textoPlano = BaseController::reemplazarVariablesPagare($DocumentoPersonalizado->content, $id);
            //base_Pdf.blade.php
            //dd($textoPlano);
            $data['cabecera'] = CreditFolderHeader::find($id);
            $data['textoPlano'] = $textoPlano;
            return PDF::loadView('reportes.base_Pdf', compact('data'))
                ->download('Pagare : ' . $data['cabecera']->customer_name . '.pdf');
        } else {
            $data['cabecera'] = CreditFolderHeader::find($id);
            $data['ultimo'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->orderBy('id', 'desc')->first();
            $data['primero'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->orderBy('id', 'ASC')->first();
            $data['detallePrimera'] = CreditFolderDetail::where('code_folder_header', $data['cabecera']->code)->first();
            $data['dias'] = ($data['cabecera']->cuotas_pagar) * (30);
            $data['customer'] = Customer::find($data['cabecera']->customer_id);
            $data['prestamo'] = Prestamos::find($data['cabecera']->tipo_prestamo);
            $date = new DateTime($data['cabecera']->date_created);
            $dia = $date->format('d');
            $mes = $date->format('m');
            $año = $date->format('Y');
            $data['garante'] = Customer::find($data['cabecera']->customer_garante_id);
            $data['garantesTabla'] = Garantes::where('credit_folder_headers_id', $id)->get();
            foreach ($data['garantesTabla'] as $val) {
                $customer = Customer::find($val->customer_id);
                $val->direccionCliente = $customer->direccion;
                $val->direccionConyuge = $customer->direccion;
                $val->telefonoCliente = $customer->telefono_parentesco;
                $val->telefonoConyuge = $customer->conyugue_telefono;
            }
            $tipoPrestamo = Prestamos::find($data['cabecera']->tipo_prestamo);
            if ($tipoPrestamo) {
                $recuRenciaPrestamo =  RecurrenciaPrestamos::find($tipoPrestamo->periodo_id);
                if ($recuRenciaPrestamo) {
                    $data['textoPlazo']  = "El pago se realizará cada " . $recuRenciaPrestamo->name;
                }
            }
            $meses = [
                '01' => 'Enero',
                '02' => 'Febrero',
                '03' => 'Marzo',
                '04' => 'Abril',
                '05' => 'Mayo',
                '06' => 'Junio',
                '07' => 'Julio',
                '08' => 'Agosto',
                '09' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre'
            ];
            $fecha_formateada = sprintf('%02d de %s del %d', $dia, $meses[$mes], $año);
            $data['fechaFormateada'] =  $fecha_formateada;
            return PDF::loadView('reportes.pagare', compact('data'))
                ->download('Pagare : ' . $data['cabecera']->customer_name . '.pdf');
        }
    }

    public function verCuota($id)
    {
        //        $cuota = CreditFolderDetail::find($id);
        //        CreditController::generarIntereses($cuota);
        $cuotamostrar = CreditFolderDetail::find($id);
        return Response::json($cuotamostrar);
    }

    public static function generarIntereses($cuota)
    {
        $company = Company::find($cuota->company_id);
        $tipoInteres = $company->select_tipo_interes;

        $numeroDiasTope = intval($company->numero_dias_interes);
        switch ($tipoInteres) {
            case 'D':
                $fechaPago = $cuota->date_vencimiento;
                $fechaMaximo = strtotime($cuota->date_vencimiento . ' +' . $numeroDiasTope . ' day');
                $fechaMaximo = date('Y-m-d', $fechaMaximo);
                $fechaActual = date('Y-m-d');
                if ($fechaActual > $fechaMaximo) {
                    $timestamp1 = strtotime($fechaPago);
                    $timestamp2 = strtotime($fechaMaximo);
                    // Calcular la diferencia en segundos entre los timestamps
                    $diferenciaSegundos = $timestamp2 - $timestamp1;
                    // Calcular el número de días de diferencia
                    $numDias = $diferenciaSegundos / 86400;
                    // Redondear el resultado a un número entero si es necesario
                    $numDias = round($numDias);
                    $interes = ($cuota->valor_cuota) * (($company->porcentaje_mora / 100)) * intval($numDias) / (365);
                    $cuota = CreditFolderDetail::find($cuota->id);
                    $cuota->interes_mora = $interes;
                    $cuota->save();
                } else {
                    $timestamp1 = strtotime($fechaPago);
                    $timestamp2 = strtotime($fechaActual);
                    // Calcular la diferencia en segundos entre los timestamps
                    $diferenciaSegundos = $timestamp2 - $timestamp1;
                    // Calcular el número de días de diferencia
                    $numDias = $diferenciaSegundos / 86400;
                    // Redondear el resultado a un número entero si es necesario
                    $numDias = round($numDias);
                    $interes = ($cuota->valor_cuota) * (($company->porcentaje_mora / 100)) * intval($numDias) / (365);
                    $cuota = CreditFolderDetail::find($cuota->id);
                    $cuota->interes_mora = $interes;
                    $cuota->save();
                }
                break;
            case 'M':
                $fechaPago = $cuota->date_vencimiento;
                $ultimoDiaMes = date('Y-m-t', strtotime($fechaPago));
                $timestamp1 = strtotime($fechaPago);
                $timestamp2 = strtotime($ultimoDiaMes);
                // Calcular la diferencia en segundos entre los timestamps
                $diferenciaSegundos = $timestamp2 - $timestamp1;
                // Calcular el número de días de diferencia
                $numDias = $diferenciaSegundos / 86400;
                // Redondear el resultado a un número entero si es necesario
                $numDias = round($numDias);
                $interes = ($cuota->valor_cuota) * (($company->porcentaje_mora / 100)) * intval($numDias) / (365);
                $cuota = CreditFolderDetail::find($cuota->id);
                $cuota->interes_mora = $interes;
                $cuota->save();
                break;
        }
    }

    public static function ultimaLetraPago($id)
    {
        $detalle = CreditFolderDetail::find($id);
        if ($detalle->saldo_remanente == "0.00") {
            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('code', $detalle->code_folder_header)
                ->first();
            $header->status = "FINALIZADO";
            $header->save();
        }
    }

    public function pagarLetra(Request $request, $id, $customer)
    {
        //        CajasController::abrirCajaAutomatica();
        $cuota = CreditFolderDetail::find($id);
        //        Determinar ultimo pago y cambiar la cabecera
        CreditController::ultimaLetraPago($id);
        $pagoTotal = $cuota->valor_cuota + $cuota->interes_mora + $cuota->faltante_anterior_cuota - $cuota->saldo_anterior_cuota;

        if ($request->input('valor_pago') == $pagoTotal) {
            $caso = 1;
        }
        if ($request->input('valor_pago') > $pagoTotal) {
            $caso = 2;
        }
        if ($request->input('valor_pago') < $pagoTotal) {
            $caso = 3;
        }
        $valorTotal = BaseController::valorTotal();

        $ultima = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $cuota->$cuota)
            ->max('numero_cuota');

        $detalle = CreditFolderDetail::find($id);

        switch ($caso) {
            //entra pago igual
            case 1:
                $detalle = CreditFolderDetail::find($id);
                $detalle->valor_pagado = $request->input('valor_pago');
                $detalle->valor_final = $request->input('valor_pago');
                $detalle->tipo_pago = $request->input('tipo_pago');
                $detalle->obervation_pago = ($request->input('observacion_pago') !== null) ? $request->input('observacion_pago') : null;
                $detalle->date_pay = date('Y-m-d');
                $detalle->hour_pay = date('H:i:s');
                $detalle->user_pay_id = Auth::user()->id;
                $detalle->status = 'PAGADA';
                $detalle->save();
                $customer = Customer::find($customer);
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'PC')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
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
                    "valor_movimiento" => $request->input('valor_pago'),
                    "saldo_general" => $valorTotal + $request->input('valor_pago'),
                    "observation" => ($request->input('observacion_pago') !== null) ? $request->input('observacion_pago') : 'PAGO DEL PRESTAMO ' . $detalle->code_folder_header . ', LA LETRA ' . $detalle->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos,
                    "user_created_id" => Auth::user()->id,
                    "date_created" => $request->input('date_pago'),
                    "hour_created" => date("H:i:s"),
                    "credit_folder_details_id" => $detalle->id,
                ];
                $existe = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                    ->where('customer_code', $customer->code)
                    ->where('type_transaction_id', $transaction->id)
                    ->where('valor_movimiento', $request->input('valor_pago'))
                    ->where('date_created', $request->input('date_pago'))
                    ->where('hour_created', date("H:i:s"));
                if ($existe->count() == 0) {
                    $movimientos = CustomerMovimiento::create($data);
                    //                    CajasController::calcularCajaAutomatica();
                    CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $request->input('valor_pago'), $customer->code, $movimientos->saldo_general, $request->input('date_pago'));
                }
                break;
            //entra pago mayor
            case 2:
                $detalle = CreditFolderDetail::find($id);
                $SOBRANTE = (float) round(($request->input('valor_pago') - $detalle->valor_cuota - $detalle->interes_mora - $detalle->faltante_anterior_cuota + $detalle->saldo_anterior_cuota), 2);
                $detalle->valor_pagado = $request->input('valor_pago');
                $detalle->valor_final = $request->input('valor_pago');
                $detalle->tipo_pago = $request->input('tipo_pago');
                $detalle->adelanto_prox_cuota = $SOBRANTE;
                $detalle->obervation_pago = ($request->input('observacion_pago') !== null) ? $request->input('observacion_pago') : null;
                $detalle->date_pay = date('Y-m-d');
                $detalle->hour_pay = date('H:i:s');
                $detalle->user_pay_id = Auth::user()->id;
                $detalle->status = 'PAGADA';
                $detalle->save();
                while ($SOBRANTE > 0) {
                    $letrasImpagas = CreditFolderDetail::where('code_folder_header', $detalle->code_folder_header)
                        ->where('status', 'PENDIENTE')
                        ->where('saldo_anterior_cuota', '0.00')
                        ->orderBy('numero_cuota')
                        ->first();
                    $saldo = CreditController::adelantarLetra($letrasImpagas->id, $SOBRANTE);
                    $SOBRANTE = (float) round($saldo, 2);
                }
                $customer = Customer::find($customer);
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'PC')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
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
                    "valor_movimiento" => $request->input('valor_pago'),
                    "saldo_general" => $valorTotal + $request->input('valor_pago'),
                    "observation" => ($request->input('observacion_pago') !== null) ? $request->input('observacion_pago') : 'PAGO DEL PRESTAMO ' . $detalle->code_folder_header . ', LA LETRA ' . $detalle->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos . '; CON ABONO A LA SIGUIENTE LETRA',
                    "user_created_id" => Auth::user()->id,
                    "date_created" => $request->input('date_pago'),
                    "hour_created" => date("H:i:s"),
                    "credit_folder_details_id" => $detalle->id,
                ];
                $existe = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                    ->where('customer_code', $customer->code)
                    ->where('type_transaction_id', $transaction->id)
                    ->where('valor_movimiento', $request->input('valor_pago'))
                    ->where('date_created', $request->input('date_pago'))
                    ->where('hour_created', date("H:i:s"));
                if ($existe->count() == 0) {
                    $movimientos = CustomerMovimiento::create($data);
                    //                    CajasController::calcularCajaAutomatica();
                    CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $request->input('valor_pago'), $customer->code, $movimientos->saldo_general, $request->input('date_pago'));
                }
                break;
            //entra pago menor
            case 3:
                $detalle = CreditFolderDetail::find($id);
                $FALTANTE = (float) round($detalle->valor_cuota + $detalle->interes_mora + $detalle->faltante_anterior_cuota - $detalle->saldo_anterior_cuota - $request->input('valor_pago'), 2);
                $detalle->valor_pagado = $request->input('valor_pago');
                $detalle->valor_final = $request->input('valor_pago');
                $detalle->tipo_pago = $request->input('tipo_pago');
                $detalle->faltante_prox_cuota = $FALTANTE;
                $detalle->obervation_pago = ($request->input('observacion_pago') !== null) ? $request->input('observacion_pago') : null;
                $detalle->date_pay = date('Y-m-d');
                $detalle->hour_pay = date('H:i:s');
                $detalle->user_pay_id = Auth::user()->id;
                $detalle->status = 'PAGADA';
                $detalle->save();
                $letrasImpagas = CreditFolderDetail::where('code_folder_header', $detalle->code_folder_header)
                    ->where('status', 'PENDIENTE')
                    ->where('faltante_anterior_cuota', '0.00')
                    ->orderBy('numero_cuota')
                    ->first();
                CreditController::faltanteLetra($letrasImpagas->id, $FALTANTE);
                $customer = Customer::find($customer);
                $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'PC')
                    ->first();
                $tabla = 'customer_movimientos';
                $code = BaseController::generarCodigo($tabla, 9);
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
                    "valor_movimiento" => $request->input('valor_pago'),
                    "saldo_general" => $valorTotal + $request->input('valor_pago'),
                    "observation" => ($request->input('observacion_pago') !== null) ? $request->input('observacion_pago') : 'PAGO DEL PRESTAMO ' . $detalle->code_folder_header . ', LA LETRA ' . $detalle->numero_cuota . '; DEL CLIENTE ' . $customer->nombres . ' ' . $customer->apellidos . '; CON UN VALOR FALTANTE QUE SE SUMARÁ A LA SIGUIENTE LETRA',
                    "user_created_id" => Auth::user()->id,
                    "date_created" => $request->input('date_pago'),
                    "hour_created" => date("H:i:s"),
                    "credit_folder_details_id" => $detalle->id,
                ];
                $existe = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                    ->where('customer_code', $customer->code)
                    ->where('type_transaction_id', $transaction->id)
                    ->where('valor_movimiento', $request->input('valor_pago'))
                    ->where('date_created', $request->input('date_pago'))
                    ->where('hour_created', date("H:i:s"));
                if ($existe->count() == 0) {
                    $movimientos = CustomerMovimiento::create($data);
                    //                CajasController::calcularCajaAutomatica();
                    CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $request->input('valor_pago'), $customer->code, $movimientos->saldo_general, $request->input('date_pago'));
                }
                break;
        }
        $vaucher = CreditController::vaucherPago($movimientos->id, $detalle->id);
        // FALTA RECALCULAR LA CABECERA
        $cabecera = CreditFolderHeader::where('code', $cuota->code_folder_header)->first();
        $valornew = $cabecera->total_pagando + $request->input('valor_pago');
        $cabecera->total_pagando = $valornew;
        $cabecera->save();
        // FALTA GUARDAR DATOS DE QUIEN PAGA EL CREDITO
        return Response::json($vaucher);
    }

    public static function vaucherPago($customerMovimiento, $detalle)
    {
        $data['customerMovimiento'] = CustomerMovimiento::find($customerMovimiento);
        $data['numeroCuota'] = CreditFolderDetail::find($detalle);
        $valorVer = $data['numeroCuota']->valor_cuota + $data['numeroCuota']->interes_mora + $data['numeroCuota']->faltante_anterior_cuota - $data['numeroCuota']->saldo_anterior_cuota - $data['customerMovimiento']->valor_movimiento;
        $data['valorComparar'] = round($valorVer, 2);
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
        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['customerMovimiento']->valor_movimiento, 2, 'DÓLARES', 'CENTAVOS');
        //        $data['cantidadLetras'] = $data['customerMovimiento']->valor_movimiento;
        $data['cuota'] = $data['numeroCuota']->capital_amortizado + $data['numeroCuota']->fondo_desgravamen;
        if (!file_exists(config('constants.ROUTS_IMAGES.PATH_COMPROBANTE'))) {
            mkdir(config('constants.ROUTS_IMAGES.PATH_COMPROBANTE'), 0777, true);
        }
        $path2 = 'uploads/comprobante/';
        $destinationPath = public_path($path2);
        $nombre = $destinationPath . $data['customerMovimiento']->code . '.pdf';
        $arch = PDF::loadView('reportes.comprobante', compact('data'))
            ->setPaper('A6', "portrait")
            ->save($nombre);
        $letra = CreditFolderDetail::find($detalle);
        $letra->path = $data['customerMovimiento']->code . '.pdf';
        $letra->save();
        return $data;
    }

    public static function adelantarLetra($id, $valor)
    {
        $detalle = CreditFolderDetail::find($id);
        if ($valor > $detalle->valor_cuota) {
            $SOBRANTE = (float) round($valor - $detalle->valor_cuota, 2);
            $abonado = $detalle->valor_cuota;
        } else {
            $SOBRANTE = (float) round(0, 2);
            $abonado = $valor;
        }
        $detalle->saldo_anterior_cuota = $abonado;
        $detalle->save();
        return $SOBRANTE;
    }

    public static function faltanteLetra($id, $valor)
    {
        $FALTANTE = (float) round($valor, 2);
        $detalle = CreditFolderDetail::find($id);
        $detalle->faltante_anterior_cuota = $FALTANTE;
        $detalle->save();
        return true;
    }

    public function aprobarCredito($id, $fecha)
    {
        $credito = CreditFolderHeader::find($id);
        $credito->date_verified = $fecha;
        $credito->hour_verified = date("H:i:s");
        $credito->user_verified_id = Auth::user()->id;
        $credito->user_verified_name = Auth::user()->username;
        $credito->status = 'APROBADO';
        $credito->save();
        return Response::json(true);
    }

    public function entregarDinero($id, $valor, $fecha)
    {
        $i = 0;
        $cabecera = CreditFolderHeader::find($id);
        $encaje = $cabecera->valor_solicitado * ($valor / 100);
        $cabecera->valor_encaje = $encaje;
        $cabecera->status = 'ENTREGADO';
        $cabecera->save();
        $valorEntregar = ($cabecera->valor_solicitado) - ($encaje);
        CreditController::generarCustomerEntregaPrestamo($valorEntregar, $cabecera->customer_id, $cabecera->customer_name, $fecha);
        $fechaActual = $fecha;
        $detalles = CreditFolderDetail::where('code_folder_header', $cabecera->code)->get();
        foreach ($detalles as $value) {
            $i = $i + 1;
            $letra = CreditFolderDetail::find($value->id);
            $letra->date_vencimiento = date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month'));
            $letra->save();
        }
        $vaucher = CreditController::vaucherEntregaDinero($cabecera->id);
        return Response::json(true);
    }

    public static function vaucherEntregaDinero($cabecera)
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
        $data['imagen'] = base64_encode($man);
        $data['cabecera'] = CreditFolderHeader::find($cabecera);
        $data['valorEncaje'] = ($data['cabecera']->valor_encaje);
        $data['encaje'] = ($data['cabecera']->valor_encaje) * (-1);
        $data['entregar'] = ($data['cabecera']->valor_solicitado) + ($data['encaje']);
        $data['company'] = $company;
        //        comentado hasta solventar el componente a letras
        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['entregar'], 2, 'DÓLARES', 'CENTAVOS');
        //        $data['cantidadLetras'] = $data['entregar'];
        if (!file_exists(config('constants.ROUTS_IMAGES.PATH_VOUCHER'))) {
            mkdir(config('constants.ROUTS_IMAGES.PATH_VOUCHER'), 0777, true);
        }
        $path2 = 'uploads/egresos/';
        $destinationPath = public_path($path2);
        $nombre = $destinationPath . $data['cabecera']->code . '.pdf';
        $arch = PDF::loadView('reportes.comprobanteEntregaDinero', compact('data'))
            ->setPaper('A6', "portrait")
            ->save($nombre);
        $credito = CreditFolderHeader::find($cabecera);
        $credito->path_encaje = $data['cabecera']->code . '.pdf';
        $credito->save();
        return $data;
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
        //        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('code_folder_header', $credito->code);
        DB::table('credit_folder_details')
            ->where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $credito->code)
            ->update(['status' => "NEGADO"]);
        CreditController::inactivarCustomer($credito->customer_code);
        return Response::json(true);
    }

    public static function inactivarCustomer($code)
    {
        $customer = Customer::where('code', $code)->first();
        $customer->status = false;
        $customer->save();
    }

    public function liquidarDetalle($id)
    {
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
        $data = [
            'capital' => (float) round($capital, 2),
            'desgravamen' => (float) round($desgravamen, 2),
            'interes' => (float) round($interes, 2),
            'total' => $total,
            'feacha_actual' => date('Y-m-d'),
            'detalle' => $detalle,
            'carpeta' => $id,
        ];
        return Response::json($data);
    }

    public function liquitarCreditoFinal($id)
    {
        //        CajasController::abrirCajaAutomatica();
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
            $detalle->tipo_pago = 'EFECTIVO';
            $detalle->obervation_pago = 'PAGA LA TOTALIDAD DEL CREDITO ' . date('Y-m-d');
            $detalle->date_pay = date('Y-m-d');
            $detalle->hour_pay = date('H:i:s');
            $detalle->user_pay_id = Auth::user()->id;
            $detalle->status = 'PAGADA';
            $detalle->save();
            CreditController::ultimaLetraPago($detalle->id);
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
                "valor_movimiento" => $pagoRealizado,
                "saldo_general" => $valorTotal + $pagoRealizado,
                "observation" => 'PAGA LA TOTALIDAD DEL CREDITO ' . date('Y-m-d'),
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
            ];
            $existe = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                ->where('customer_code', $customer->code)
                ->where('type_transaction_id', $transaction->id)
                ->where('valor_movimiento', $pagoRealizado)
                ->where('code', $code);
            if ($existe->count() == 0) {
                $movimientos = CustomerMovimiento::create($data);
                //CajasController::calcularCajaAutomatica();
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $pagoRealizado, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
                $cabecera = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
                $valornew = $cabecera->total_pagando + $pagoRealizado;
                $cabecera->total_pagando = $valornew;
                $cabecera->save();
            }
        }
        return Response(true);
    }

    public function incobrableDetalle($id)
    {
        $header = CreditFolderHeader::find($id);
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->get();
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
            ->where('date_vencimiento', '<=', date('Y-m-d'))
            ->sum('fondo_desgravamen');
        $total = (float) round(($capital + $desgravamen + $interes), 2);
        $data = [
            'capital' => (float) round($capital, 2),
            'desgravamen' => (float) round($desgravamen, 2),
            'interes' => (float) round($interes, 2),
            'total' => $total,
            'feacha_actual' => date('Y-m-d'),
            'detalle' => $detalle,
            'carpeta' => $id,
        ];
        return Response::json($data);
    }

    public function creditoIncobrableFinal(Request $request, $id)
    {
        $datos = $request->input('parametros');
        CajasController::abrirCajaAutomatica();
        $header = CreditFolderHeader::find($id);
        $detalle = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('code_folder_header', $header->code)
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($detalle as $det) {
            $detalle = CreditFolderDetail::find($det->id);
            $capital = $det->capital_amortizado;
            $desgravamen = $det->fondo_desgravamen;
            $interes = 0;
            $pagoRealizado = (float) round(($capital + $desgravamen + $interes), 2);
            $detalle->valor_pagado = $pagoRealizado;
            $detalle->valor_final = $pagoRealizado;
            $detalle->tipo_pago = 'EFECTIVO';
            $detalle->obervation_pago = $datos['incobrable'] . ' en:' . date('Y-m-d');
            $detalle->date_pay = date('Y-m-d');
            $detalle->hour_pay = date('H:i:s');
            $detalle->user_pay_id = Auth::user()->id;
            $detalle->status = 'PAGADA';
            $detalle->save();
            $customer = Customer::find($header->customer_id);
            $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'PCI')
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
                "valor_movimiento" => $pagoRealizado,
                "saldo_general" => $valorTotal + $pagoRealizado,
                "observation" => 'INGRESO DE VALORES POR ' . $datos['incobrable'] . ' en:' . date('Y-m-d'),
                "user_created_id" => Auth::user()->id,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
            ];
            $existe = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                ->where('customer_code', $customer->code)
                ->where('type_transaction_id', $transaction->id)
                ->where('valor_movimiento', $pagoRealizado)
                ->where('date_created', date('Y-m-d'))
                ->where('hour_created', date("H:i:s"));
            if ($existe->count() == 0) {
                $movimientos = CustomerMovimiento::create($data);
                CajasController::calcularCajaAutomatica();
                CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $pagoRealizado, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
                $cabecera = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
                $valornew = $cabecera->total_pagando + $pagoRealizado;
                $cabecera->total_pagando = $valornew;
                $cabecera->save();
                //            RESTA DE INCOBRABLE
                $transactionR = TypeTransaction::where('company_id', Auth::user()->company_id)
                    ->where('name_corto', 'RCI')
                    ->first();
                $tablaR = 'customer_movimientos';
                $codeR = BaseController::generarCodigo($tablaR, 9);
                $valorTotalR = BaseController::valorTotal();
                $dataR = [
                    "code" => $codeR,
                    "company_id" => Auth::user()->company_id,
                    "customer_code" => $customer->code,
                    "afecta" => $transactionR->afecta,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_ruc" => $customer->numero_documento,
                    "customer_address" => $customer->direccion,
                    "customer_telefono" => $customer->telefono,
                    "type_transaction_id" => $transactionR->id,
                    "type_transaction_name" => $transactionR->name,
                    "type_transaction_action" => $transactionR->action,
                    "valor_movimiento" => $pagoRealizado,
                    "saldo_general" => $valorTotalR - $pagoRealizado,
                    "observation" => 'SALIDA DE VALORES POR ' . $datos['incobrable'] . ' EN:' . date('Y-m-d'),
                    "user_created_id" => Auth::user()->id,
                    "date_created" => date('Y-m-d'),
                    "hour_created" => date("H:i:s"),
                ];
                $movimientosR = CustomerMovimiento::create($dataR);
                CajasController::calcularCajaAutomatica();
                CustomerHistorialController::guardarHistorialAutomatica($movimientosR->code, $transactionR->id, $pagoRealizado, $customer->code, $movimientosR->saldo_general, date('Y-m-d'));
            }
        }
        return Response::json(true);
    }

    public function showFiles($id)
    {
        $files = CreditFiles::where('company_id', Auth::user()->company_id)
            ->where('credit_header_id', $id)
            ->get();
        return Response::json($files);
    }

    public function destroyArchivo($id)
    {
        $archivos = CreditFiles::find($id);
        $archivos->delete();
    }

    public static function verPdfView($id)
    {
        $header = CreditFiles::find($id);
        $completo = $header->archivo;
        return Response::json($completo);
    }

    public function guardarArchivos(Request $request, $id)
    {
        if ($request->hasFile('file')) {
            $path = config('constants.ROUTES.PATH_DOCUMENTS_CREDIT');
            if (!file_exists($path)) {
                File::makeDirectory($path, 0777, true);
            }
            $fileInputXL = $request->file('file');
            $nombreArchivoNuevo = CreditController::Normalizar_Nombre_Archivo($fileInputXL);
            $destinationPath = $path;
            $fileInputXL->move($destinationPath, $nombreArchivoNuevo);
            $archivos = CreditFiles::where('company_id', Auth::user()->company_id)->where('credit_header_id', $id)->where('archivo', $nombreArchivoNuevo);
            if ($archivos->count() == 0) {
                $dataproductoArchivo = [
                    'company_id' => Auth::user()->company_id,
                    'credit_header_id' => $id,
                    'date_created' => date('Y-m-d'),
                    'hour_created' => date('H:i:s'),
                    'descripcion' => strtoupper($request->input('descripcion')),
                    'archivo' => $nombreArchivoNuevo,
                    'path' => config('constants.ROUTES.PATH_DOCUMENTS_CREDIT') . $nombreArchivoNuevo,
                    'formato' => $fileInputXL->getClientOriginalExtension(),
                ];
                $Archivo = CreditFiles::create($dataproductoArchivo);
                return Response::json($Archivo);
            }
        }
        return Response::json(false);
    }

    public static function Normalizar_Nombre_Archivo($file, $adicional = '')
    {
        $archivo = $file->getClientOriginalName();
        $extencion = $file->getClientOriginalExtension();
        return $archivo;
    }

    public function pdfCuota($id)
    {
        $detalle = CreditFolderDetail::find($id);
        if ($detalle->path != null && $detalle->path != '') {
            $pdf = '<iframe id="imagen_comprobante" class="img" width="100%" height="500px" src="/uploads/comprobante/' . $detalle->path . '">';
        } else {
            $vaucher = CreditController::crearVaucherNuevo($id);
            $letra = CreditFolderDetail::find($id);
            $pdf = '<iframe id="imagen_comprobante" class="img" width="100%" height="500px" src="/uploads/comprobante/' . $letra->path . '">';
        }
        return Response::json($pdf);
    }

    public static function crearVaucherNuevo($id)
    {
        $valorTotal = BaseController::valorTotal();
        $detalle = CreditFolderDetail::find($id);
        $valor = $detalle->valor_cuota;
        $detalle->valor_pagado = $valor;
        $detalle->valor_final = $valor;
        $detalle->obervation_pago = 'PAGO DE LETRA';
        $detalle->date_pay = date('Y-m-d');
        $detalle->hour_pay = date('H:i:s');
        $detalle->user_pay_id = Auth::user()->id;
        $detalle->status = 'PAGADA';
        $detalle->save();
        $header = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        $customer = Customer::where('numero_documento', $header->customer_ruc)->first();
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'PC')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
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
            "valor_movimiento" => $valor,
            "saldo_general" => $valorTotal + $valor,
            "observation" => null,
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
        CajasController::calcularCajaAutomatica();
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valor, $customer->code, $movimientos->saldo_general, date('Y-m-d'), null);
        $vaucher = CreditController::vaucherPago($movimientos->id, $detalle->id);
        // FALTA RECALCULAR LA CABECERA
        $cabecera = CreditFolderHeader::where('code', $detalle->code_folder_header)->first();
        $valornew = $cabecera->total_pagando + $valor;
        $cabecera->total_pagando = $valornew;
        $cabecera->save();
        return true;
    }

    public function pdfCuotaVer($detalle, $customer)
    {

        $data['numeroCuota'] = CreditFolderDetail::find($detalle);
        $company = Company::find(Auth::user()->company_id);
        $customer = Customer::find($customer);
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
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['numeroCuota']->valor_pagado, 2, 'DÓLARES', 'CENTAVOS');
        $valorVer = $data['numeroCuota']->valor_cuota + $data['numeroCuota']->interes_mora + $data['numeroCuota']->faltante_anterior_cuota - $data['numeroCuota']->saldo_anterior_cuota - $data['numeroCuota']->valor_pagado;
        $data['gastosCobranza'] = $data['numeroCuota']->notificado *  $company->valor_notificado;
        $data['valorComparar'] = round($valorVer + $data['gastosCobranza'], 2);
        $data['cuota'] = $data['numeroCuota']->capital_amortizado + $data['numeroCuota']->fondo_desgravamen;
        if (!file_exists(config('constants.ROUTS_IMAGES.PATH_COMPROBANTE'))) {
            mkdir(config('constants.ROUTS_IMAGES.PATH_COMPROBANTE'), 0777, true);
        }
        $path2 = 'uploads/comprobante/';
        $destinationPath = public_path($path2);

        return PDF::loadView('reportes.comprobantePagoLetra', compact('data'))
            ->setPaper('A6', "portrait")
            ->download($data['customer']->apellidos . ' ' . $data['customer']->nombres . ' Letra:' . $data['numeroCuota']->numero_cuota . '.pdf');
        $letra = CreditFolderDetail::find($detalle);
        $letra->path = $data['customerMovimiento']->code . '.pdf';
        $letra->save();
        return $data;
    }
    public function pdfNotificaciones($detalle, $customer)
    {

        $data['numeroCuota'] = CreditFolderDetail::find($detalle);
        $data['valorMora']  = BaseController::calculoInteresMoraLetraValor($detalle);
        $company = Company::find(Auth::user()->company_id);
        $customer = Customer::find($customer);
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
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);

        $fecha1 = Carbon::parse($data['numeroCuota']->date_vencimiento);
        $fecha2 = Carbon::parse(date('Y-m-d'));


        $diferenciaDias = $fecha1->diffInDays($fecha2);
        $data['diasMora'] = $diferenciaDias;

        return PDF::loadView('reportes.notificacionPago', compact('data'))
            ->setPaper('A4', "landscape")
            ->download('Notificación de pago.pdf');
    }

    public function pdfEncaje($id)
    {
        $cabecera = CreditFolderHeader::find($id);
        if ($cabecera->path_encaje != null && $cabecera->path_encaje != '') {
            $pdf = '<iframe id="imagen_comprobante" class="img" width="100%" height="500px" src="/uploads/egresos/' . $cabecera->path_encaje . '">';
        } else {
            $vaucher = CreditController::vaucherEntregaDinero($id);
            $header = CreditFolderHeader::find($id);
            $pdf = '<iframe id="imagen_comprobante" class="img" width="100%" height="500px" src="/uploads/egresos/' . $header->path_encaje . '">';
        }
        return Response::json($pdf);
    }

    public function pdfVerEncaje($cabecera)
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
        $data['imagen'] = base64_encode($man);
        $data['cabecera'] = CreditFolderHeader::find($cabecera);
        $data['cliente'] = Customer::find($data['cabecera']->customer_id);
        $data['valorEncaje'] = ($data['cabecera']->encaje_valor);
        $data['gastoAdministrativo'] = ($data['cabecera']->gasto_administrativo);

        $data['primer_gasto'] = ($data['cabecera']->primer_gasto);
        $data['segundo_gasto'] = ($data['cabecera']->segundo_gasto);
        $data['tercer_gasto'] = ($data['cabecera']->tercer_gasto);

        $data['encaje'] = ($data['cabecera']->valor_encaje) * (-1);
        $data['entregar'] = 0;
        if ($data['cabecera']->suma_valores_gastos_prestamo > 0) {

            $data['entregar'] = ($data['cabecera']->valor_solicitado) -  $data['valorEncaje'] - $data['gastoAdministrativo'];
        } else {

            $data['entregar'] = ($data['cabecera']->valor_solicitado) -  $data['valorEncaje'] - $data['gastoAdministrativo'] - $data['primer_gasto'] -  $data['segundo_gasto'] - $data['tercer_gasto'];
        }
        $data['company'] = $company;
        //        comentado hasta solventar el componente a letras
        $formatter = new NumeroALetras();
        $data['cantidadLetras'] = $formatter->toMoney($data['entregar'], 2, 'DÓLARES', 'CENTAVOS');
        //        $data['cantidadLetras'] = $data['entregar'];
        if (!file_exists(config('constants.ROUTS_IMAGES.PATH_VOUCHER'))) {
            mkdir(config('constants.ROUTS_IMAGES.PATH_VOUCHER'), 0777, true);
        }
        $path2 = 'uploads/egresos/';
        $destinationPath = public_path($path2);
        return PDF::loadView('reportes.comprobanteEntregaDinero', compact('data'))
            ->setPaper('A5', "portrait")
            ->download('Liquidación crédito ' . $data['cabecera']->code . '.pdf');
    }

    public function editarCredito(Request $request)
    {
        $company = Company::find(Auth::user()->company_id);
        $credito = CreditFolderHeader::find($request->input('numeroCredito'));
        $customer = Customer::find($credito->customer_id);
        $sumaInteres = 0;
        $deuda = $request->input('valor_prestamo');
        $interes = round($customer->customer_tarifa_interes, 2);
        $numCuotas = $request->input('numero_cuotas');
        $inte = ($interes / 100);
        $cuotas = ($deuda * $inte * (pow((1 + $inte), ($numCuotas)))) / ((pow((1 + $inte), ($numCuotas))) - 1);
        $fondoDesgravamen = number_format((($deuda * ($company->porcentaje_desgravament / 100)) / $numCuotas), 2);
        $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);
        $fechaActual = date('Y-m-d');

        $credito->valor_solicitado = $deuda;
        $credito->cuotas_pagar = $numCuotas;
        $credito->valor_cuota = $valorCuotas;
        $credito->valor_desgravamen = $fondoDesgravamen;
        $credito->date_created = date('Y-m-d');
        $credito->hour_created = date("H:i:s");
        $credito->user_created_id = Auth::user()->id;
        $credito->user_created_name = Auth::user()->username;
        $credito->save();

        $detalleDelete = CreditFolderDetail::where('code_folder_header', $credito->code)->get();
        foreach ($detalleDelete as $value) {
            $detalle = CreditFolderDetail::find($value->id);
            $detalle->delete();
        }

        for ($i = 1; $i <= $numCuotas; $i++) {
            $amortizado = $cuotas - ($deuda * $inte);
            $interes = round($deuda * $inte, 2);
            $sumaInteres = $sumaInteres + $interes;
            $deuda = $deuda - ($cuotas - ($deuda * $inte));
            $dataDetalle = [
                "numero_cuota" => $i,
                "company_id" => Auth::user()->company_id,
                "code_folder_header" => $credito->code,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
                "date_vencimiento" => date('Y-m-d', strtotime($fechaActual . ' + ' . $i . ' month')),
                "interes_periodo" => $interes,
                "capital_amortizado" => round($amortizado, 2),
                "fondo_desgravamen" => $fondoDesgravamen,
                "valor_cuota" => round($valorCuotas, 2),
                "saldo_remanente" => round($deuda, 2),
            ];
            CreditFolderDetail::create($dataDetalle);
        }
        $credito->valor_interes_pago = $sumaInteres;
        $credito->save();
        return Response::json(true);
    }

    public function verCredito($id)
    {
        $customer = Customer::find($id);
        $creditos = CreditFolderHeader::where('customer_ruc', $customer->numero_documento)->get();
        if ($creditos->count() > 0) {
            $credito = CreditFolderHeader::where('customer_ruc', $customer->numero_documento)->first();
            $customer = Customer::where('numero_documento', $credito->customer_ruc)->first();
            return Response::json($customer->id);
        } else {
            return Response::json('');
        }
    }

    public function verFondos($id)
    {
        $credito = CreditFolderHeader::find($id);
        $valorTotal = BaseController::valorTotal();
        if ($valorTotal > $credito->valor_solicitado) {
            return Response::json(true);
        } else {
            return Response::json(false);
        }
    }

    public static function generarCustomerEntregaPrestamo($valorEntregar, $customerID, $customer_name, $fecha)
    {
        CajasController::abrirCajaAutomatica();
        $customer = Customer::find($customerID);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'ENC')
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
            "valor_movimiento" => $valorEntregar,
            "saldo_general" => $valorTotal - $valorEntregar,
            "observation" => $transaction->name . ' A ' . $customer_name,
            "user_created_id" => Auth::user()->id,
            //"date_created" => $fecha,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
        ];
        $existe = CustomerMovimiento::where('company_id', Auth::user()->company_id)
            ->where('customer_code', $customer->code)
            ->where('type_transaction_id', $transaction->id)
            ->where('valor_movimiento', $valorEntregar)
            ->where('date_created', $fecha);
        //        if ($existe->count() == 0) {
        $movimientos = CustomerMovimiento::create($data);
        //            CajasController::calcularCajaAutomatica();
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $valorEntregar, $customer->code, $movimientos->saldo_general, date('Y-m-d'));
        CustomerHistorial::create($data);
        return Response::json($movimientos);
        //        } else {
        //            return Response::json(false);
        //        }
    }

    public function descargarEcxelCreditos($desde, $hasta)
    {

        $data = [
            'inicio' => $desde,
            'fin' => $hasta,
        ];
        return Excel::download(new CreditosPagados($data), 'Creditos.xlsx');
    }

    public function descargarEcxelCreditosVencidos($desde, $hasta)
    {
        $data = [
            'inicio' => $desde,
            'fin' => $hasta,
        ];
        return Excel::download(new CreditoVencidos($data), 'Creditos Vencidos.xlsx');
    }

    public function descargarEcxelCreditosPendientes($desde, $hasta)
    {
        $data = [
            'inicio' => $desde,
            'fin' => $hasta,
        ];
        return Excel::download(new CreditPendientes($data), 'Creditos por Vencer.xlsx');
    }

    public function editFolder($code)
    {
        $credit = CreditFolderHeader::where('code', $code)->first();
        return Response::json($credit);
    }

    public function editFolderCabecera($id, $carpeta)
    {
        abort(501, 'La edición de cabecera por esta ruta no está implementada.');
    }

    public function createTipoPrestamo(Request $request)
    {

        $company = Company::find(Auth::user()->company_id);
        $data = [
            'company_id' => $company->id,
            'name' => mb_strtoupper($request->input('name')),
            'interes' => $request->input('interes'),
            'interes_anual' => $request->input('interes_anual'),
            'fondo_desgravamen' => $request->input('fondo_desgravamen'),
            'tipo' => $request->input('type'),
            'valor_maximo' => $request->input('valor_maximo'),
            'valor_minimo' => $request->input('valor_minimo'),
            'edad_minima' => $request->input('edad_minima'),
            'edad_maxima' => $request->input('edad_maxima'),
            'diario' => $request->input('diario'),
            'periodo_id' => $request->input('periodo_id'),
            'letra_cambio' => ($request->input('letra_cambio') != null) ? 1 : 0,
            'pagare' => ($request->input('pagare') != null) ? 1 : 0,
        ];

        Prestamos::create($data);
        $preta = Prestamos::where('status', 'A')->get();
        return Response::json($preta);
    }

    public function updateTipoPrestamo(Request $request)
    {
        $prestamo = Prestamos::find($request->input('tipoPestamoID'));
        $prestamo->name = mb_strtoupper($request->input('name'));
        $prestamo->interes = $request->input('interes');
        $prestamo->interes_anual = $request->input('interes_anual');
        $prestamo->fondo_desgravamen = $request->input('fondo_desgravamen');
        $prestamo->tipo = $request->input('type');
        $prestamo->valor_maximo = $request->input('valor_maximo');
        $prestamo->valor_minimo = $request->input('valor_minimo');
        $prestamo->edad_minima = $request->input('edad_minima');
        $prestamo->edad_maxima = $request->input('edad_maxima');
        $prestamo->diario = $request->input('diario');
        $prestamo->periodo_id = $request->input('periodo_id');
        $prestamo->letra_cambio = ($request->input('letra_cambio') != null) ? 1 : 0;
        $prestamo->pagare = ($request->input('pagare') != null) ? 1 : 0;
        $prestamo->save();

        $preta = Prestamos::where('status', 'A')->get();
        return Response::json($preta);
    }

    public function buscarPrestamo($prestamo)
    {
        return Response::json(Prestamos::find($prestamo));
    }

    public function consultarPrestamo($id)
    {
        $company = Company::find(Auth::user()->company_id);
        $recu = RecurrenciaPrestamos::where('company_id', $company->id)->get();
        $data = [
            'prestamo' => Prestamos::find($id),
            'recu' => $recu,
        ];
        return Response::json($data);
    }
}
