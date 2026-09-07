<?php

namespace App\Http\Controllers\Cobranza;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Doctrine\DateTimeType;
use App\Models\WhaEnvios;
use App\Models\TypeTransaction;
use Carbon\Carbon;

class CobranzaController extends Controller
{

    public function index()
    {
        $month1 = date('m');
        $year1 = date('Y');
        $day1 = date("d", mktime(0, 0, 0, $month1 + 1, 0, $year1));
        $ultimo = date('Y-m-d', mktime(0, 0, 0, $month1, $day1, $year1));
        $month2 = date('m');
        $year2 = date('Y');
        $primero = date('Y-m-d', mktime(0, 0, 0, $month2, 1, $year2));
        $primero = date('Y-m-d');
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('date_vencimiento', '<=', date('Y-m-d'))
            ->whereBetween('date_vencimiento', [$primero, $ultimo])
            ->where('status', 'PENDIENTE')
            ->get();
        $company = Company::find(Auth::user()->company_id);
        return view('cobranza/index')
            ->with('primero', $primero)
            ->with('ultimo', $primero);
    }

    public function reporteInteresMora($inicio, $fin)
    {
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->whereBetween('date_vencimiento', [$inicio, $fin])
            ->where('status', 'PENDIENTE')
            ->orderBy('id', 'asc')
            ->get();
        $company = Company::find(Auth::user()->company_id);
        foreach ($vencidas as $key => $value) {
            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('code', $value->code_folder_header)
                ->first();

            if ($header) {
                $value->customer_id = $header->customer_id;
                $value->customer_name = $header->customer_name;
                $value->customer_ruc = $header->customer_ruc;

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
                //$letra = CreditFolderDetail::find($value->id);
                //$letra->interes_mora = $porcentaje;
                //$letra->save();
            }
        }
        return Response::json($vencidas);
    }

    public function listaLetras()
    {
        $month1 = date('m');
        $year1 = date('Y');
        $day1 = date("d", mktime(0, 0, 0, $month1 + 1, 0, $year1));
        $ultimo = date('Y-m-d', mktime(0, 0, 0, $month1, $day1, $year1));
        $month2 = date('m');
        $year2 = date('Y');
        $primero = date('Y-m-d', mktime(0, 0, 0, $month2, 1, $year2));
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('date_vencimiento', '<=', date('Y-m-d'))
            //                ->whereBetween('date_vencimiento', [$primero, $ultimo])
            ->where('status', 'PENDIENTE')
            ->get();
        foreach ($vencidas as $value) {
            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                ->where('code', $value->code_folder_header)
                ->first();
            $value->customer_name = $header->customer_name;
            $value->customer_ruc = $header->customer_ruc;
            $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
            $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $value->dias_mora = $date1->diffInDays($date2);
        }
        if ($vencidas->count() > 0) {
            return Response::json($vencidas);
        }
    }

    //    public function listaLetrasMes() {
    //        $data = array();
    //        $month1 = date('m');
    //        $year1 = date('Y');
    //        $day1 = date("d", mktime(0, 0, 0, $month1 + 1, 0, $year1));
    //        $ultimo = date('Y-m-d', mktime(0, 0, 0, $month1, $day1, $year1));
    //        $month2 = date('m');
    //        $year2 = date('Y');
    //        $primero = date('Y-m-d', mktime(0, 0, 0, $month2, 1, $year2));
    //        $nombreMes = strtoupper(Carbon::create()->month($month1)->locale('es')->monthName);
    //        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
    //                ->where('date_vencimiento', '<=', date('Y-m-d'))
    //                ->whereBetween('date_vencimiento', [$primero, $ultimo])
    //                ->where('status', 'PENDIENTE')
    //                ->get();
    //        foreach ($vencidas as $value) {
    //            $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
    //                    ->where('code', $value->code_folder_header)
    ////                    ->where('status', 'APROBADO')
    //                    ->first();
    //            $value->customer_name = $header->customer_name;
    //            $value->customer_ruc = $header->customer_ruc;
    //            $date1 = Carbon::createFromFormat('Y-m-d', $value->date_vencimiento);
    //            $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
    //            $value->dias_mora = $date1->diffInDays($date2);
    //        }
    //        $data = [
    //            'letras' => $vencidas,
    //            'mes' => $nombreMes,
    //        ];
    //        return Response::json($data);
    //    }

    public function listaLetrasMes(Request $request)
    {
        $columns = array(
            0 => 'code_folder_header',
            1 => 'date_vencimiento',
            2 => 'numero_cuota',
            3 => 'valor_cuota',
            4 => 'accion',
        );
        $company = Company::find(Auth::user()->company_id);
        $month1 = date('m');
        $year1 = date('Y');
        $day1 = date("d", mktime(0, 0, 0, $month1 + 1, 0, $year1));
        $ultimo = date('Y-m-d', mktime(0, 0, 0, $month1, $day1, $year1));
        $month2 = date('m');
        $year2 = date('Y');
        $primero = date('Y-m-d', mktime(0, 0, 0, $month2, 1, $year2));
        $nombreMes = strtoupper(Carbon::create()->month($month1)->locale('es')->monthName);
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('date_vencimiento', '<=', date('Y-m-d'))
            ->whereBetween('date_vencimiento', [$primero, $ultimo])
            ->where('status', 'PENDIENTE');

        //        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
        //                ->where('date_vencimiento', '<=', date('Y-m-d'))
        //                ->where('status', 'PENDIENTE');
        $totalData = $vencidas->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        foreach ($request->input('columns') as $columna) {
            switch ($columna['data']) {
                case 'date_vencimiento':
                    $search = $columna['search']['value'];
                    if (empty($search)) {
                        $posts = $vencidas->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
                    } else {
                        $posts = $vencidas->where('date_vencimiento', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
                        $totalFiltered = $vencidas->where('date_vencimiento', 'LIKE', "%{$search}%")->count();
                    }
                    break;
                case 'code_folder_header':
                    $search = $columna['search']['value'];
                    if (empty($search)) {
                        $posts = $vencidas->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
                    } else {
                        $posts = $vencidas
                            ->select(
                                'credit_folder_details.code_folder_header',
                                'credit_folder_headers.customer_name',
                                'credit_folder_details.date_vencimiento',
                                'credit_folder_details.numero_cuota',
                                'credit_folder_details.valor_cuota'
                            )
                            ->where('credit_folder_details.company_id', Auth::user()->company_id) // Específica la tabla 'credit_folder_details'
                            ->where('credit_folder_details.date_vencimiento', '<=', date('Y-m-d'))
                            ->where('credit_folder_details.status', 'PENDIENTE')
                            ->join('credit_folder_headers', 'credit_folder_headers.code', '=', 'credit_folder_details.code_folder_header')
                            ->where('credit_folder_headers.customer_name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('credit_folder_details.code_folder_header', 'asc')
                            ->get();

                        $totalFiltered = count($posts);
                    }
                    break;
                default:
                    $posts = $vencidas->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                    break;
            }
        }
        $data = array();

        if (!empty($posts)) {
            foreach ($posts as $state) {
                $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                    ->where('code', $state->code_folder_header)
                    ->first();
                if ($header) {
                    $nestedData['code_folder_header'] = $header->customer_name;
                    $nestedData['date_vencimiento'] = '<span class="badge badge-danger"> ' . $state->date_vencimiento . '</span>';
                    $date1 = Carbon::createFromFormat('Y-m-d', $state->date_vencimiento);
                    $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $nestedData['numero_cuota'] = 'Número de cuota <b>' . $state->numero_cuota . '</b> con un total de <b>' . $date1->diffInDays($date2) . '</b> días';
                    $nestedData['valor_cuota'] = '$' . $state->valor_cuota;
                    $data[] = $nestedData;
                }
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        return Response::json($json_data);
    }

    public function listaLetrasMesBuscar(Request $request)
    {
        $columns = array(
            0 => 'code_folder_header',
            1 => 'date_vencimiento',
            2 => 'numero_cuota',
            3 => 'valor_cuota',
            4 => 'accion',
        );
        $company = Company::find(Auth::user()->company_id);
        $vencidas = CreditFolderDetail::where('company_id', Auth::user()->company_id)
            ->where('date_vencimiento', '<=', date('Y-m-d'))
            ->where('status', 'PENDIENTE');
        $totalData = $vencidas->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        foreach ($request->input('columns') as $columna) {
            switch ($columna['data']) {
                case 'date_vencimiento':
                    $search = $columna['search']['value'];
                    if (empty($search)) {
                        $posts = $vencidas->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
                    } else {
                        $posts = $vencidas->where('date_vencimiento', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
                        $totalFiltered = $vencidas->where('date_vencimiento', 'LIKE', "%{$search}%")->count();
                    }
                    break;
                case 'code_folder_header':
                    $search = $columna['search']['value'];
                    if (empty($search)) {
                        $posts = $vencidas->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
                    } else {
                        $posts = $vencidas
                            ->select(
                                'credit_folder_details.code_folder_header',
                                'credit_folder_headers.customer_name',
                                'credit_folder_details.date_vencimiento',
                                'credit_folder_details.numero_cuota',
                                'credit_folder_details.valor_cuota'
                            )
                            ->where('credit_folder_details.company_id', Auth::user()->company_id) // Específica la tabla 'credit_folder_details'
                            ->where('credit_folder_details.date_vencimiento', '<=', date('Y-m-d'))
                            ->where('credit_folder_details.status', 'PENDIENTE')
                            ->join('credit_folder_headers', 'credit_folder_headers.code', '=', 'credit_folder_details.code_folder_header')
                            ->where('credit_folder_headers.customer_name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('credit_folder_details.code_folder_header', 'asc')
                            ->get();

                        $totalFiltered = count($posts);
                    }
                    break;
                default:
                    $posts = $vencidas->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                    break;
            }
        }
        $data = array();

        if (!empty($posts)) {
            foreach ($posts as $state) {
                $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
                    ->where('code', $state->code_folder_header)
                    ->first();
                if ($header) {
                    $nestedData['code_folder_header'] = $header->customer_name;
                    $nestedData['date_vencimiento'] = '<span class="badge badge-danger"> ' . $state->date_vencimiento . '</span>';
                    $date1 = Carbon::createFromFormat('Y-m-d', $state->date_vencimiento);
                    $date2 = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $nestedData['numero_cuota'] = 'Número de cuota <b>' . $state->numero_cuota . '</b> con un total de <b>' . $date1->diffInDays($date2) . '</b> días';
                    $nestedData['valor_cuota'] = '$' . $state->valor_cuota;
                    $data[] = $nestedData;
                }
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        return Response::json($json_data);
    }

    public function notificarLetra($id)
    {
        $data = array();
        $letra = CreditFolderDetail::where('id', $id)->first();
        $pagoTotal = $letra->valor_cuota + $letra->interes_mora + $letra->faltante_anterior_cuota - $letra->saldo_anterior_cuota;
        $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('code', $letra->code_folder_header)
            ->first();
        $company = Company::find(Auth::user()->company_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'NTC')
            ->first();

        $whatsappEnviar = 'Estimad@, *' . $header->customer_name . '*' . ' tenemos un pago pendiente del prestamo *# ' . $letra->code_folder_header . '* , por el monto total de: *' . $pagoTotal .
            '* su fechas máximo de pago fue el ' . $letra->date_vencimiento . '*, Correspondiente a la cuota *#' . $letra->numero_cuota . '* Gracias por usar nuestros servicios somos. *' . $company->comercial_name . '.*';
        $whatsapp = new WhaEnvios();
        $whatsapp->company_id = Auth::user()->company_id;
        $whatsapp->envio_ahora = true;
        $whatsapp->es_transacion = true;
        $whatsapp->inmediato = true;
        $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
        $whatsapp->type_transacction_id = $transaction->id;
        $whatsapp->type_transacction_name = $transaction->name;
        $whatsapp->customer_id = $header->customer_id;
        $whatsapp->customer_name = $header->customer_name;
        $whatsapp->customer_celular = $header->customer_phone;
        $whatsapp->whatsapp = $whatsappEnviar;
        $whatsapp->estado = 'PENDIENTE';
        $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
        $whatsapp->fecha_envio = date('Y-m-d H:i:s');
        $whatsapp->prestamo_id = $id;
        $whatsapp->save();
        $data = [
            'cabecera' => $header,
            'letra' => $letra,
        ];
        return Response::json($data);
    }

    public function envioWhatsappWeb($id)
    {
        $data = array();
        $letra = CreditFolderDetail::where('id', $id)->first();
        $pagoTotal = $letra->valor_cuota + $letra->interes_mora + $letra->faltante_anterior_cuota - $letra->saldo_anterior_cuota;
        $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('code', $letra->code_folder_header)
            ->first();
        $company = Company::find(Auth::user()->company_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'NTC')
            ->first();

        $fechaActual = date('Y-m-d');
        $fechaPago  = $letra->date_vencimiento;
        if ($fechaPago < $fechaActual) {
            $fechaPago = Carbon::createFromFormat('Y-m-d', $letra->date_vencimiento);
            $fechaActualTrabajo = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $diasDebeFechaActual = $fechaPago->diffInDays($fechaActualTrabajo);
            $whatsappEnviar = '*"' . $company->comercial_name . '"* solicita el pago de su crédito saldo *$' . $pagoTotal . ' USD*, mas recargos por ' . $diasDebeFechaActual . ' día(s) de mora. Cancelar a tiempo para evitar recargos.';
        } else {
            $fechaPagoInicial = Carbon::createFromFormat('Y-m-d', $letra->date_vencimiento);
            $fechaProrroga = $fechaPagoInicial->addDays($company->dias_inicio_cobro);
            $fechaFormateada = $fechaProrroga->format('Y-m-d');
            $whatsappEnviar = '*"' . $company->comercial_name . '"* le recuerda que su saldo a pagar es de: *$' . $pagoTotal . ' USD*, y su fecha de pago es el ' . $letra->date_vencimiento . ' hasta ' . $fechaFormateada . '. Cancelar a tiempo para evitar recargos por mora.';
        }

        //$whatsappEnviar = 'Estimad@, *' . $header->customer_name . '*' . ' tenemos un pago pendiente del prestamo *# ' . $letra->code_folder_header . '* , por el monto total de: *' . $pagoTotal .
        //    '* su fechas máximo de pago fue el ' . $letra->date_vencimiento . '*, Correspondiente a la cuota *#' . $letra->numero_cuota . '* Gracias por usar nuestros servicios somos. *' . $company->comercial_name . '.*';

        $existe = WhaEnvios::where('company_id', Auth::user()->company_id)
            ->where('customer_id', $header->customer_id)
            ->where('customer_celular', $header->customer_phone)
            ->where('whatsapp', $whatsappEnviar)
            ->where('prestamo_id', $id)
            ->exists();
        if ($existe) {
            $whatsapp = WhaEnvios::where('company_id', Auth::user()->company_id)
                ->where('customer_id', $header->customer_id)
                ->where('customer_celular', $header->customer_phone)
                ->where('whatsapp', $whatsappEnviar)
                ->where('prestamo_id', $id)
                ->first();
        } else {
            $whatsapp = new WhaEnvios();
            $whatsapp->company_id = Auth::user()->company_id;
            $whatsapp->envio_ahora = false;
            $whatsapp->es_transacion = true;
            $whatsapp->inmediato = true;
            $whatsapp->description = 'ENVIO DE WHATSAPP DESDE TRANSACCIONES';
            $whatsapp->type_transacction_id = $transaction->id;
            $whatsapp->type_transacction_name = $transaction->name;
            $whatsapp->customer_id = $header->customer_id;
            $whatsapp->customer_name = $header->customer_name;
            $whatsapp->customer_celular = $header->customer_phone;
            $whatsapp->whatsapp = $whatsappEnviar;
            $whatsapp->estado = 'PENDIENTE';
            $whatsapp->fecha_creacion = date('Y-m-d H:i:s');
            $whatsapp->fecha_envio = date('Y-m-d H:i:s');
            $whatsapp->prestamo_id = $id;
            $whatsapp->save();
        }
        $data = [
            'code' => '200',
            'telefono' => '593' . strval($whatsapp->customer_celular),
            'mensaje' => $whatsapp->whatsapp,
        ];
        return Response::json($data);
    }

    public function notificarLetraMensajes($id)
    {
        $data = array();
        $notificaciones = WhaEnvios::where('prestamo_id', $id);
        $letra = CreditFolderDetail::where('id', $id)->first();
        $header = CreditFolderHeader::where('company_id', Auth::user()->company_id)
            ->where('code', $letra->code_folder_header)
            ->first();
        $texto = 'Mensajes Enviados a ' . $header->customer_name . ' por concepto de mora en el del prestamo #' . $letra->code_folder_header . ' con fecha de pago ' . $letra->date_vencimiento;
        $data = [
            'notificaciones' => $notificaciones->get(),
            'total' => $notificaciones->count(),
            'texto' => $texto,
        ];
        return $data;
    }
}
