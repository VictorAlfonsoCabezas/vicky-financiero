<?php

namespace App\Exports\ReporteCartera;

use App\Http\Controllers\Base\BaseController;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class ReporteCarteraExport implements FromView
{
    public $fecha_inicio;
    public $fecha_fin;
    public $tipo;

    public function __construct($fecha_inicio, $fecha_fin, $tipo)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->tipo = $tipo;
    }
    public function view(): View
    {
        $company = Company::find(Auth::user()->company_id);
        if ($this->tipo == 1) {
            $letrasVencidasPagadas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
                ->where('date_vencimiento', '<=', $this->fecha_fin)
                ->where('status', 'PENDIENTE')
                ->orderBy('code_folder_header', 'asc');
            $sumaSolicitado = 0;
            $totalPagandoSuma = 0;
            $valorCuotaSuma = 0;
            $valorCuotaPagarSuma = 0;
            $interesMoraSuma = 0;
            $totalPagarSumaTotal = 0;
            $creditoVisto = '';

            foreach ($letrasVencidasPagadas->get() as $value) {
                $credito  =  CreditFolderHeader::where('code', $value->code_folder_header)->first();
                if ($credito) {
                    $value->entregado =  $credito->status;
                    if ($credito->status == 'ENTREGADO') {
                        $interesMora =  BaseController::calculoInteresMoraLetraValorMensual($value->id);
                        $interesMoraSuma +=   $interesMora;
                        $valorCuotaPagarSuma += $value->valor_cuota;
                        if ($creditoVisto !=  $credito->code) {
                            $sumaSolicitado += $credito->valor_solicitado;
                            $totalPagandoSuma += $credito->total_pagando;
                            $valorCuotaSuma += $credito->valor_cuota;


                            $totalPagarSumaTotal += $credito->valor_cuota + $interesMora;
                            $creditoVisto  = $credito->code;
                        }
                    }
                }
            }
            $totalSumavalor_solicitado = 0;
            $totalSuma_pagando = 0;
            $totalSuma_cuota = 0;
            $totalSuma_interesMora = 0;
            $totalSuma_totalPagar = 0;
            if ($company->reporte_cartera_unido) {
                $letrasVencidas = CreditFolderDetail::select(
                    'code_folder_header',
                    'status',
                    DB::raw('SUM(valor_cuota) as total_valor_cuota'),

                    DB::raw('SUM(interes_mora) as total_interes_mora')
                )
                    ->where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->where('status', 'PENDIENTE')
                    ->groupBy('code_folder_header')
                    ->get();
                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
                    if ($credito) {
                        $val->entregado =  $credito->status;
                        if ($credito->status == 'ENTREGADO') {
                            $val->codigoCredito = '';
                            $val->identificacionClienteID = '';
                            $val->identificacionCliente = '';
                            $val->nombreSocio = '';
                            $val->valor_solicitado = '';
                            $val->total_pagando = '';
                            $val->valor_cuota = '';
                            $val->datosDeudor =   '';
                            $val->datosGarante1 =   '';
                            $val->datosGarante2 =   '';
                            $val->valor_cuotaPagar = $val->total_valor_cuota;
                            $interesMora =  BaseController::calculoInteresMoraLetrasUnidas($val->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                            $val->interesMora =   $interesMora;
                            $totalSuma_interesMora +=  $val->interesMora;
                            $dias =  BaseController::calculoDiasLetrasUnidasUnico($val->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                            $LetrasContador =  BaseController::calculoDiasLetrasUnidasUnicoContador($val->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                            $val->diferenciaDias =   $dias . '/' . $LetrasContador;
                            if ($credito) {
                                $val->identificacionClienteID = $credito->customer_id;
                                $val->identificacionCliente = $credito->customer_ruc;
                                $val->nombreSocio = $credito->customer_name;
                                $val->valor_solicitado = $credito->valor_solicitado;
                                $val->total_pagando = $credito->total_pagando;
                                $val->valor_cuota = $credito->valor_cuota;
                                $val->datosDeudor =   $credito->customer_address . '<br>' .  $credito->customer_phone . '<br>' . $credito->customer_email;
                                $customerGarante =  Customer::find($credito->customer_garante_id);
                                $val->totalPagar =   number_format(round($val->valor_cuota, 2), 2, '.', '') + number_format(round($val->interesMora, 2), 2, '.', '');
                                if ($customerGarante) {
                                    $val->datosGarante1 =  $customerGarante->nombres . ' ' . $customerGarante->apellidos . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono . '<br>' . $customerGarante->correo;
                                    $val->datosGarante2 =  $customerGarante->name_parentesco . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono_parentesco;
                                }
                                $totalSumavalor_solicitado =  $val->valor_solicitado + $totalSumavalor_solicitado;
                                $totalSuma_pagando =  $val->total_pagando + $totalSuma_pagando;
                                $totalSuma_cuota =  $val->valor_cuota + $totalSuma_cuota;

                                $totalSuma_totalPagar =  $val->totalPagar +  $totalSuma_totalPagar;
                            }
                        }
                    }
                }
            } else {
                $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->where('status', 'PENDIENTE')
                    ->get();


                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
                    if ($credito) {
                        $val->entregado =  $credito->status;
                        if ($credito->status == 'ENTREGADO') {
                            $val->identificacionClienteID = '';
                            $val->identificacionCliente = '';
                            $val->nombreSocio = '';
                            $val->valor_solicitado = 0;
                            $val->total_pagando = 0;
                            $val->valor_cuota = 0;
                            $val->interesMora = '';
                            $interesMora =  BaseController::calculoInteresMoraLetraValorMensual($val->id);
                            $val->interesMora =   $interesMora;

                            $date1 = Carbon::parse($val->date_vencimiento);
                            $date2 = Carbon::parse(date('Y-m-d'));

                            $difference = $date1->diffInDays($date2);
                            $val->diferenciaDias =  $difference;


                            $val->totalPagar =   number_format(round($val->valor_cuota, 2), 2, '.', '') + number_format(round($val->interesMora, 2), 2, '.', '');
                            $val->datosDeudor =   '';
                            $val->datosGarante1 =   '';
                            $val->datosGarante2 =   '';
                            $val->valor_cuotaPagar = '';
                            if ($credito) {
                                $val->identificacionClienteID = $credito->customer_id;
                                $val->identificacionCliente = $credito->customer_ruc;
                                $val->nombreSocio = $credito->customer_name;
                                $val->valor_solicitado = $credito->valor_solicitado;
                                $val->total_pagando = $credito->total_pagando;
                                $val->valor_cuota = $credito->valor_cuota;
                                $val->valor_cuotaPagar = $credito->valor_cuota;
                                $val->datosDeudor =   $credito->customer_address . '<br>' .  $credito->customer_phone . '<br>' . $credito->customer_email;
                                $customerGarante =  Customer::find($credito->customer_garante_id);
                                if ($customerGarante) {
                                    $val->datosGarante1 =  $customerGarante->nombres . ' ' . $customerGarante->apellidos . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono . '<br>' . $customerGarante->correo;
                                    $val->datosGarante2 =  $customerGarante->name_parentesco . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono_parentesco;
                                }
                            }

                            $totalSumavalor_solicitado =  $val->valor_solicitado + $totalSumavalor_solicitado;
                            $totalSuma_pagando =  $val->total_pagando + $totalSuma_pagando;
                            $totalSuma_cuota =  $val->valor_cuota + $totalSuma_cuota;
                            $totalSuma_interesMora =  $val->interesMora +  $totalSuma_interesMora;
                            $totalSuma_totalPagar =  $val->totalPagar +  $totalSuma_totalPagar;
                        }
                    }
                }
            }
        } else {
            $letrasVencidasPagadas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
                ->where('date_vencimiento', '<=', $this->fecha_fin)
                ->orderBy('code_folder_header', 'asc');
            $sumaSolicitado = 0;
            $totalPagandoSuma = 0;
            $valorCuotaSuma = 0;
            $valorCuotaPagarSuma = 0;
            $interesMoraSuma = 0;
            $totalPagarSumaTotal = 0;
            $creditoVisto = '';

            foreach ($letrasVencidasPagadas->get() as $value) {
                $credito  =  CreditFolderHeader::where('code', $value->code_folder_header)->first();
                if ($credito) {
                    $value->entregado =  $credito->status;
                    $interesMora =  BaseController::calculoInteresMoraLetraValorMensual($value->id);
                    $interesMoraSuma +=   $interesMora;
                    $valorCuotaPagarSuma += $value->valor_cuota;
                    if ($creditoVisto !=  $credito->code) {
                        $sumaSolicitado += $credito->valor_solicitado;
                        $totalPagandoSuma += $credito->total_pagando;
                        $valorCuotaSuma += $credito->valor_cuota;


                        $totalPagarSumaTotal += $credito->valor_cuota + $interesMora;
                        $creditoVisto  = $credito->code;
                    }
                }
            }
            $totalSumavalor_solicitado = 0;
            $totalSuma_pagando = 0;
            $totalSuma_cuota = 0;
            $totalSuma_interesMora = 0;
            $totalSuma_totalPagar = 0;
            if ($company->reporte_cartera_unido) {
                $letrasVencidas = CreditFolderDetail::select(
                    'code_folder_header',
                    'status',
                    DB::raw('SUM(valor_cuota) as total_valor_cuota'),

                    DB::raw('SUM(interes_mora) as total_interes_mora')
                )
                    ->where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->groupBy('code_folder_header')
                    ->get();
                $interesMoraSuma = 0;
                $valorCuotaPagarSuma = 0;
                foreach ($letrasVencidas as $val) {
                    $interesMora =  BaseController::calculoInteresMoraLetrasUnidas($val->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                    $interesMoraSuma +=   $interesMora;
                    $valorCuotaPagarSuma += $val->total_valor_cuota;
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();

                    if ($credito) {
                        $val->valorPendiente = CreditFolderDetail::where('code_folder_header', $val->code_folder_header)->where('status', 'PENDIENTE')->sum('valor_cuota');
                        if ($val->valorPendiente > 0) {
                            $val->status = 'PENDIENTE';
                        } else {
                            $val->status = 'PAGADA';
                        }
                        $val->entregado =  $credito->status;
                        $val->codigoCredito = '';
                        $val->identificacionClienteID = '';
                        $val->identificacionCliente = '';
                        $val->nombreSocio = '';
                        $val->valor_solicitado = '';
                        $val->total_pagando = '';
                        $val->valor_cuota = '';
                        $val->datosDeudor =   '';
                        $val->datosGarante1 =   '';
                        $val->datosGarante2 =   '';
                        $val->valor_cuotaPagar = $val->total_valor_cuota;
                        $interesMora =  BaseController::calculoInteresMoraLetrasUnidas($val->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                        $val->interesMora =   $interesMora;
                        $totalSuma_interesMora +=  $val->interesMora;
                        $dias =  BaseController::calculoDiasLetrasUnidas($val->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                        $val->diferenciaDias =   $dias;
                        if ($credito) {
                            $val->identificacionClienteID = $credito->customer_id;
                            $val->identificacionCliente = $credito->customer_ruc;
                            $val->nombreSocio = $credito->customer_name;
                            $val->valor_solicitado = $credito->valor_solicitado;
                            $val->total_pagando = $credito->total_pagando;
                            $val->valor_cuota = $credito->valor_cuota;
                            $val->datosDeudor =   $credito->customer_address . '<br>' .  $credito->customer_phone . '<br>' . $credito->customer_email;
                            $customerGarante =  Customer::find($credito->customer_garante_id);
                            $val->totalPagar =   number_format(round($val->valor_cuota, 2), 2, '.', '') + number_format(round($val->interesMora, 2), 2, '.', '');
                            if ($customerGarante) {
                                $val->datosGarante1 =  $customerGarante->nombres . ' ' . $customerGarante->apellidos . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono . '<br>' . $customerGarante->correo;
                                $val->datosGarante2 =  $customerGarante->name_parentesco . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono_parentesco;
                            }
                            $totalSumavalor_solicitado =  $val->valor_solicitado + $totalSumavalor_solicitado;
                            $totalSuma_pagando =  $val->total_pagando + $totalSuma_pagando;
                            $totalSuma_cuota =  $val->valor_cuota + $totalSuma_cuota;

                            $totalSuma_totalPagar =  $val->totalPagar +  $totalSuma_totalPagar;
                        }
                    }
                }
            } else {
                $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->get();


                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
                    if ($credito) {
                        $val->entregado =  $credito->status;
                        $val->identificacionClienteID = '';
                        $val->identificacionCliente = '';
                        $val->nombreSocio = '';
                        $val->valor_solicitado = 0;
                        $val->total_pagando = 0;
                        $val->valor_cuota = 0;
                        $val->interesMora = '';
                        $interesMora =  BaseController::calculoInteresMoraLetraValorMensual($val->id);
                        $val->interesMora =   $interesMora;

                        $date1 = Carbon::parse($val->date_vencimiento);
                        $date2 = Carbon::parse(date('Y-m-d'));

                        $difference = $date1->diffInDays($date2);
                        $val->diferenciaDias =  $difference;


                        $val->totalPagar =   number_format(round($val->valor_cuota, 2), 2, '.', '') + number_format(round($val->interesMora, 2), 2, '.', '');
                        $val->datosDeudor =   '';
                        $val->datosGarante1 =   '';
                        $val->datosGarante2 =   '';
                        $val->valor_cuotaPagar = '';
                        if ($credito) {
                            $val->identificacionClienteID = $credito->customer_id;
                            $val->identificacionCliente = $credito->customer_ruc;
                            $val->nombreSocio = $credito->customer_name;
                            $val->valor_solicitado = $credito->valor_solicitado;
                            $val->total_pagando = $credito->total_pagando;
                            $val->valor_cuota = $credito->valor_cuota;
                            $val->valor_cuotaPagar = $credito->valor_cuota;
                            $val->datosDeudor =   $credito->customer_address . '<br>' .  $credito->customer_phone . '<br>' . $credito->customer_email;
                            $customerGarante =  Customer::find($credito->customer_garante_id);
                            if ($customerGarante) {
                                $val->datosGarante1 =  $customerGarante->nombres . ' ' . $customerGarante->apellidos . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono . '<br>' . $customerGarante->correo;
                                $val->datosGarante2 =  $customerGarante->name_parentesco . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono_parentesco;
                            }
                        }

                        $totalSumavalor_solicitado =  $val->valor_solicitado + $totalSumavalor_solicitado;
                        $totalSuma_pagando =  $val->total_pagando + $totalSuma_pagando;
                        $totalSuma_cuota =  $val->valor_cuota + $totalSuma_cuota;
                        $totalSuma_interesMora =  $val->interesMora +  $totalSuma_interesMora;
                        $totalSuma_totalPagar =  $val->totalPagar +  $totalSuma_totalPagar;
                    }
                }
            }
        }
        return view('reportes.reporte-cartera')
            ->with('sumaSolicitado', $sumaSolicitado)
            ->with('totalPagandoSuma', $totalPagandoSuma)
            ->with('valorCuotaSuma', $valorCuotaSuma)
            ->with('valorCuotaPagarSuma', $valorCuotaPagarSuma)
            ->with('interesMoraSuma', $interesMoraSuma)
            ->with('company', $company)
            ->with('letrasVencidas',  $letrasVencidas);
    }
}
