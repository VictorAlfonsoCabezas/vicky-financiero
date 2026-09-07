<?php

namespace App\Exports\Creditos;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\CustomerTipoAhorros;
use App\Models\CustomerHistorial;
use App\Models\TipoAhorros;
use App\Models\Customer;
use App\Models\Company;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Prestamos;
use DateTime;
use Illuminate\Support\Facades\Auth;

class CreditosLetrasExport implements FromView
{
    protected $variable;

    public function __construct($variable)
    {
        $this->variable = $variable;
    }

    public function view(): View
    {
        $variableConsulta = $this->variable;

        $inicio = $variableConsulta['inicio'];
        $fin = $variableConsulta['fin'];
        $tipo = $variableConsulta['tipo_reporte'];
        $search = $variableConsulta['search'];
        $company = Company::find(Auth::user()->company_id);
        $fechaActual = date('Y-m-d');

        $creditosTotal = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where('status', 'ENTREGADO')
            ->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', '%' .   $search . '%')
                    ->orWhere('customer_ruc', 'like', '%' .   $search . '%');
            })
            ->orderBy('code', 'asc');

        $valorInteresesCreditos = 0;
        $valorPagadoCreditos = 0;
        $valorPendienteCreditos = 0;
        $valorvalorCuotaTotal = 0;
        $sumaValorSolicitado = 0;
        foreach ($creditosTotal->get() as $valCredito) {
            $LetrasPendientesFecha = CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('date_vencimiento', '<=', date('Y-m-d'))->where('status', 'PENDIENTE')->count();
            if ($LetrasPendientesFecha != 0) {
                $valorvalorCuotaTotal += $valCredito->valor_cuota;
                $sumaValorSolicitado += $valCredito->valor_solicitado;
                $valorInteresesCreditos += CreditFolderDetail::where('code_folder_header', $valCredito->code)->sum('interes_periodo');
                $valorPagadoCreditos += CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PAGADA')->sum('valor_cuota');
                $valorPendienteCreditos += CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            }
        }


        $creditosTotalTotalizado = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', '%' .   $search . '%')
                    ->orWhere('customer_ruc', 'like', '%' .   $search . '%');
            })
            ->orderBy('code', 'asc')
            ->get();
        $valorInteresesCreditosTotalizado = 0;
        $valorPagadoCreditosTotalizado = 0;
        $valorPendienteCreditosTotalizado = 0;
        $valorvalorCuotaTotalTotalizado = 0;
        $sumaValorSolicitadoTotalizado = 0;
        foreach ($creditosTotalTotalizado as $valCredito) {
            $LetrasPendientesFecha = CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('date_vencimiento', '<=', date('Y-m-d'))->where('status', 'PENDIENTE')->count();
            $valorvalorCuotaTotalTotalizado += $valCredito->valor_cuota;
            $sumaValorSolicitadoTotalizado += $valCredito->valor_solicitado;
            $valorInteresesCreditosTotalizado += CreditFolderDetail::where('code_folder_header', $valCredito->code)->sum('interes_periodo');
            $valorPagadoCreditosTotalizado += CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PAGADA')->sum('valor_cuota');
            $valorPendienteCreditosTotalizado += CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            //DATOS EXTRA

            $customer = Customer::find($valCredito->customer_id);
            $valCredito->customerIdentificacion =  $customer->numero_documento;
            $valCredito->customerNombre =  $customer->apellidos . ' '  . $customer->nombres;
            $pretamo = Prestamos::find($valCredito->tipo_prestamo);
            $valCredito->prestamoNombre = '';
            $valCredito->prestamoInteres = '';
            if ($pretamo) {
                $valCredito->prestamoNombre = $pretamo->name;
                $valCredito->prestamoInteres = $pretamo->interes;
            }
            $valCredito->valorintereses = CreditFolderDetail::where('code_folder_header', $valCredito->code)->sum('interes_periodo');
            $valCredito->valorPagado = CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PAGADA')->sum('valor_cuota');
            $valCredito->valorPendiente = CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            $valCredito->pendientesFecha = CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('date_vencimiento', '<=', date('Y-m-d'))->where('status', 'PENDIENTE')->count();
        }

        $creditos = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where('status', 'ENTREGADO')
            ->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', '%' .   $search . '%')
                    ->orWhere('customer_ruc', 'like', '%' .   $search . '%');
            })
            ->orderBy('code', 'asc')
            ->get();
        foreach ($creditos as $val) {
            $customer = Customer::find($val->customer_id);
            $val->customerIdentificacion =  $customer->numero_documento;
            $val->customerNombre =  $customer->apellidos . ' '  . $customer->nombres;
            $pretamo = Prestamos::find($val->tipo_prestamo);
            $val->prestamoNombre = '';
            $val->prestamoInteres = '';
            if ($pretamo) {
                $val->prestamoNombre = $pretamo->name;
                $val->prestamoInteres = $pretamo->interes;
            }
            $val->valorintereses = CreditFolderDetail::where('code_folder_header', $val->code)->sum('interes_periodo');
            $val->valorPagado = CreditFolderDetail::where('code_folder_header', $val->code)->where('status', 'PAGADA')->sum('valor_cuota');
            $val->valorPendiente = CreditFolderDetail::where('code_folder_header', $val->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
            $val->pendientesFecha = CreditFolderDetail::where('code_folder_header', $val->code)->where('date_vencimiento', '<=', date('Y-m-d'))->where('status', 'PENDIENTE')->count();
        }

        $letrasImpagasTotal = CreditFolderDetail::join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->where('credit_folder_details.date_vencimiento', '>=', $inicio)
            ->where('credit_folder_details.date_vencimiento', '<=', $fin)
            ->where('credit_folder_details.status', 'PENDIENTE')
            ->where('credit_folder_headers.status', 'ENTREGADO')
            ->where(function ($query) use ($search) {
                $query->where('credit_folder_headers.customer_name', 'like', '%' .   $search . '%')
                    ->orWhere('credit_folder_headers.customer_ruc', 'like', '%' .   $search . '%');
            })
            ->select('credit_folder_details.*', 'credit_folder_details.valor_cuota as valor_cuota_letra', 'credit_folder_headers.code as header_code', 'credit_folder_headers.status as header_status');
        $interesCalculadoLetras = 0;
        $sumaTotalValorLetra = 0;
        foreach ($letrasImpagasTotal->get() as $valueLetras) {
            $sumaTotalValorLetra += $valueLetras->valor_cuota_letra;
            $valorMora = BaseController::calculoInteresMoraLetraValorMensual($valueLetras->id);
            $interesCalculadoLetras += $valorMora;
        }

        $letrasImpagas = CreditFolderDetail::join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->where('credit_folder_details.date_vencimiento', '>=', $inicio)
            ->where('credit_folder_details.date_vencimiento', '<=', $fin)
            ->where('credit_folder_details.status', 'PENDIENTE')
            ->where('credit_folder_headers.status', 'ENTREGADO')
            ->where(function ($query) use ($search) {
                $query->where('credit_folder_headers.customer_name', 'like', '%' .   $search . '%')
                    ->orWhere('credit_folder_headers.customer_ruc', 'like', '%' .   $search . '%');
            })
            ->select('credit_folder_details.*', 'credit_folder_details.valor_cuota as valor_cuota_letra', 'credit_folder_headers.code as header_code', 'credit_folder_headers.status as header_status')
            ->get();
        foreach ($letrasImpagas as $val) {
            $cabecera  = CreditFolderHeader::where('code', $val->code_folder_header)->first();
            $val->colorLetra  = '#FFCBC1';
            $val->interesCalculado = 0;
            if ($val->date_vencimiento > date('Y-m-d')) {
                $val->colorLetra  = '#DBFFD6';
            } else {
                $valorMora = BaseController::calculoInteresMoraLetraValorMensual($val->id);
                $val->interesCalculado = $valorMora;
            }
            $val->totalPagar = $val->interesCalculado + $val->valor_cuota;
            if ($cabecera) {
                $val->totalLetras = $cabecera->cuotas_pagar;
                $customer = Customer::find($cabecera->customer_id);
                $val->customerName =  $customer->apellidos . ' ' . $customer->nombres;
                $val->direccion = $customer->direccion;
                $val->telefono = $customer->telefono;
                $val->nombreGarante = $cabecera->customer_garante_name;
                $val->numeroDocumento = $customer->numero_documento;
                $garanteID = $cabecera->customer_garante_id;
                $val->ciGarante = '';
                $val->telefonoGarante = '';
                $date1 = new DateTime($val->date_vencimiento);
                $date2 = new DateTime($fechaActual);
                $diff = $date1->diff($date2);
                $val->diasVencido = $diff->days;
                if ($val->diasVencido >= 0 && $val->diasVencido <= 30) {
                    $val->rango = '0-30';
                } elseif ($val->diasVencido >= 31 && $val->diasVencido <= 60) {
                    $val->rango = '31-60';
                } elseif ($val->diasVencido >= 61 && $val->diasVencido <= 90) {
                    $val->rango = '61-90';
                } elseif ($val->diasVencido >= 91 && $val->diasVencido <= 120) {
                    $val->rango = '91-120';
                } elseif ($val->diasVencido >= 121 && $val->diasVencido <= 180) {
                    $val->rango = '121-180';
                } elseif ($val->diasVencido >= 181 && $val->diasVencido <= 360) {
                    $val->rango = '181-360';
                } else {
                    $val->raggo = '361 en adelante';
                }
            }
        }
        return view('reportes.creditos-letras-export')
            ->with('creditos', $creditos)
            ->with('creditosTotal', $creditosTotal)
            ->with('valorInteresesCreditos', $valorInteresesCreditos)
            ->with('valorPagadoCreditos', $valorPagadoCreditos)
            ->with('valorPendienteCreditos', $valorPendienteCreditos)
            ->with('letrasImpagas', $letrasImpagas)
            ->with('letrasImpagasTotal', $letrasImpagasTotal)
            ->with('interesCalculadoLetras', $interesCalculadoLetras)
            ->with('tipo', $tipo)
            ->with('sumaTotalValorLetra', $sumaTotalValorLetra)
            ->with('valorvalorCuotaTotal', $valorvalorCuotaTotal)
            ->with('sumaValorSolicitado', $sumaValorSolicitado)
            ->with('creditosTotalTotalizado', $creditosTotalTotalizado)
            ->with('valorvalorCuotaTotalTotalizado', $valorvalorCuotaTotalTotalizado)
            ->with('sumaValorSolicitadoTotalizado', $sumaValorSolicitadoTotalizado)
            ->with('valorInteresesCreditosTotalizado', $valorInteresesCreditosTotalizado)
            ->with('valorPagadoCreditosTotalizado', $valorPagadoCreditosTotalizado)
            ->with('valorPendienteCreditosTotalizado', $valorPendienteCreditosTotalizado)
            ->with('company', $company);
    }
}
