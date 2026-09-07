<?php

namespace App\Http\Livewire\ReporteCartera;

use App\Http\Controllers\Base\BaseController;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\Company;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteCartera\ReporteCarteraExport;
use Illuminate\Support\Facades\DB;

class ReporteCarteraComponent extends Component
{

    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';


    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $tipo_reporte = 1;


    public function mount()
    {
        $this->fecha_inicio = date('Y-m-d');
        $this->fecha_fin = date('Y-m-d');
        $this->tipo_reporte = 1;
    }

    public function export()
    {
        if ($this->fecha_inicio  != '' and $this->fecha_fin != '') {

            return Excel::download(new ReporteCarteraExport($this->fecha_inicio, $this->fecha_fin,  $this->tipo_reporte), 'Reporte Cartera fecha de corte del ' . $this->fecha_inicio . ' al ' . $this->fecha_fin . '.xlsx');
        } else {
            $color = 'danger';
            $mensaje = 'Debe seleccionar las fechas para generar el reporte';
            $data = [
                'titulo' => '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Advertencia',
                'color' => $color,
                'mensaje' => $mensaje
            ];

            $this->dispatchBrowserEvent('alerta', $data);
        }
    }

    public function render()
    {

        $company = Company::find(Auth::user()->company_id);
        if ($this->tipo_reporte == 1) {
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
                $value->entregado =  '';
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

            if ($company->reporte_cartera_unido) {


                $letrasVencidasConsulta = CreditFolderDetail::select(
                    'code_folder_header',
                    'status',
                    DB::raw('SUM(valor_cuota) as total_valor_cuota'),

                    DB::raw('SUM(interes_mora) as total_interes_mora')
                )
                    ->where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->where('status', 'PENDIENTE')
                    ->groupBy('code_folder_header');
                $interesMoraSuma = 0;
                $valorCuotaPagarSuma = 0;
                foreach ($letrasVencidasConsulta->get() as $alueCalculos) {
                    $credito  =  CreditFolderHeader::where('code', $alueCalculos->code_folder_header)->first();
                    $alueCalculos->entregado =  '';
                    if ($credito) {
                        $alueCalculos->entregado =  $credito->status;
                        if ($credito->status == 'ENTREGADO') {
                            $interesMora =  BaseController::calculoInteresMoraLetrasUnidas($alueCalculos->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                            $interesMoraSuma +=   $interesMora;
                            $valorCuotaPagarSuma += $alueCalculos->total_valor_cuota;
                        }
                    }
                }

                $letrasVencidas = $letrasVencidasConsulta->paginate(15);
                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();

                    $val->entregado =  '';
                    if ($credito) {
                        $val->entregado =  $credito->status;
                        if ($credito->status == 'ENTREGADO') {
                            //ENTREGADO
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
                                if ($customerGarante) {
                                    $val->datosGarante1 =  $customerGarante->nombres . ' ' . $customerGarante->apellidos . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono . '<br>' . $customerGarante->correo;
                                    $val->datosGarante2 =  $customerGarante->name_parentesco . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono_parentesco;
                                }
                            }
                        }
                    }
                }
            } else {
                $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->where('status', 'PENDIENTE')
                    ->paginate(15);
                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
                    $val->entregado =  '';
                    if ($credito) {
                        $val->entregado =  $credito->status;
                        if ($credito->status == 'ENTREGADO') {
                            $val->identificacionClienteID = '';
                            $val->identificacionCliente = '';
                            $val->nombreSocio = '';
                            $val->valor_solicitado = '';
                            $val->total_pagando = '';
                            $val->valor_cuota = '';
                            $val->valor_cuotaPagar = '';
                            $val->interesMora = '';
                            $interesMora =  BaseController::calculoInteresMoraLetraValorMensual($val->id);
                            $val->interesMora =   $interesMora;

                            $date1 = Carbon::parse($val->date_vencimiento);
                            $date2 = Carbon::parse(date('Y-m-d'));

                            $difference = $date1->diffInDays($date2);
                            $val->diferenciaDias =  $difference;


                            $val->totalPagar =   number_format(round($val->valor_cuotaPagar, 2), 2, '.', '') + number_format(round($val->interesMora, 2), 2, '.', '');
                            $val->datosDeudor =   '';
                            $val->datosGarante1 =   '';
                            $val->datosGarante2 =   '';
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

            if ($company->reporte_cartera_unido) {
                $letrasVencidasConsulta = CreditFolderDetail::select(
                    'code_folder_header',
                    'status',
                    DB::raw('SUM(valor_cuota) as total_valor_cuota'),

                    DB::raw('SUM(interes_mora) as total_interes_mora')
                )
                    ->where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->groupBy('code_folder_header');
                $interesMoraSuma = 0;
                $valorCuotaPagarSuma = 0;
                foreach ($letrasVencidasConsulta->get() as $alueCalculos) {
                    $interesMora =  BaseController::calculoInteresMoraLetrasUnidas($alueCalculos->code_folder_header, $this->fecha_inicio, $this->fecha_fin);
                    $interesMoraSuma +=   $interesMora;
                    $valorCuotaPagarSuma += $alueCalculos->total_valor_cuota;
                }
                $letrasVencidas = $letrasVencidasConsulta->paginate(15);

                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
                    $val->valorPendiente = CreditFolderDetail::where('code_folder_header', $val->code_folder_header)->where('status', 'PENDIENTE')->sum('valor_cuota');
                    if ($val->valorPendiente > 0) {
                        $val->status = 'PENDIENTE';
                    } else {
                        $val->status = 'PAGADA';
                    }
                    $val->entregado =  $credito->status;
                    //ENTREGADO
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
                        if ($customerGarante) {
                            $val->datosGarante1 =  $customerGarante->nombres . ' ' . $customerGarante->apellidos . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono . '<br>' . $customerGarante->correo;
                            $val->datosGarante2 =  $customerGarante->name_parentesco . '<br>' . $customerGarante->direccion . '<br>' .  $customerGarante->telefono_parentesco;
                        }
                    }
                }
            } else {
                $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
                    ->where('date_vencimiento', '<=', $this->fecha_fin)
                    ->paginate(15);
                foreach ($letrasVencidas as $val) {
                    $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
                    $val->entregado =  $credito->status;

                    $val->identificacionClienteID = '';
                    $val->identificacionCliente = '';
                    $val->nombreSocio = '';
                    $val->valor_solicitado = '';
                    $val->total_pagando = '';
                    $val->valor_cuota = '';
                    $val->valor_cuotaPagar = '';
                    $val->interesMora = '';
                    $interesMora =  BaseController::calculoInteresMoraLetraValorMensual($val->id);
                    $val->interesMora =   $interesMora;

                    $date1 = Carbon::parse($val->date_vencimiento);
                    $date2 = Carbon::parse(date('Y-m-d'));

                    $difference = $date1->diffInDays($date2);
                    $val->diferenciaDias =  $difference;


                    $val->totalPagar =   number_format(round($val->valor_cuotaPagar, 2), 2, '.', '') + number_format(round($val->interesMora, 2), 2, '.', '');
                    $val->datosDeudor =   '';
                    $val->datosGarante1 =   '';
                    $val->datosGarante2 =   '';
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
                }
            }
        }

        return view('livewire.reporte-cartera.reporte-cartera-component', compact('letrasVencidas', 'company', 'letrasVencidasPagadas', 'sumaSolicitado', 'totalPagandoSuma', 'valorCuotaSuma', 'valorCuotaPagarSuma', 'interesMoraSuma', 'totalPagarSumaTotal'));
    }
}
