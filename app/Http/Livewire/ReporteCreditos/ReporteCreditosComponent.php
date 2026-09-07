<?php

namespace App\Http\Livewire\ReporteCreditos;

use App\Http\Controllers\Base\BaseController;
use App\Models\Company;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\Prestamos;
use App\Exports\Creditos\CreditosLetrasExport;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ReporteCreditosComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $tipo_reporte = 0;
    public $exportar = array();
    public $search = '';


    public function mount()
    {
        $this->fecha_inicio = date('Y-m-d');
        $this->fecha_fin = date('Y-m-d');
    }

    public function obtenerDatos()
    {
        $this->fecha_inicio = $this->fecha_inicio ?? '';
        $this->fecha_fin = $this->fecha_fin ?? '';
        $this->tipo_reporte = $this->tipo_reporte ?? '';
    }


    public function export()
    {
        if ($this->tipo_reporte == 0) {
            $color = 'danger';
            $mensaje = 'Debe seleccionar un tipo de reporte antes de exportar';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
            $this->dispatchBrowserEvent('alerta', $data);

            return;
        }
        $nombre = 'Reporte';
        if ($this->tipo_reporte == 1) {
            $nombre = 'Reporte de creditos';
        } else {

            $nombre = 'Reporte de letras';
        }
        return Excel::download(new CreditosLetrasExport($this->exportar), $nombre . '.xlsx');
    }

    public function render()
    {
        $inicio = $this->fecha_inicio;
        $fin = $this->fecha_fin;
        $company = Company::find(Auth::user()->company_id);
        $fechaActual = date('Y-m-d');
        //dd( $inicio , $fin, $this->tipo_reporte);

        $creditos = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where('status', 'ENTREGADO')
            ->where(function ($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->orderBy('code', 'asc')
            ->paginate(100);
        $creditosTotal = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where('status', 'ENTREGADO')
            ->where(function ($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_ruc', 'like', '%' . $this->search . '%');
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

        //inicio totalizado

        $creditosTotalizado = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where(function ($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->orderBy('code', 'asc')
            ->paginate(100);
        $creditosTotalTotalizado  = CreditFolderHeader::where('date_created', '>=', $inicio)
            ->where('date_created', '<=', $fin)
            ->where(function ($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->orderBy('code', 'asc');
        $valorInteresesCreditosTotalizado  = 0;
        $valorPagadoCreditosTotalizado  = 0;
        $valorPendienteCreditosTotalizado  = 0;
        $valorvalorCuotaTotalTotalizado  = 0;
        $sumaValorSolicitadoTotalizado  = 0;
        foreach ($creditosTotalTotalizado->get() as $valCredito) {
            $valorvalorCuotaTotalTotalizado  += $valCredito->valor_cuota;
            $sumaValorSolicitadoTotalizado  += $valCredito->valor_solicitado;
            $valorInteresesCreditosTotalizado  += CreditFolderDetail::where('code_folder_header', $valCredito->code)->sum('interes_periodo');
            $valorPagadoCreditosTotalizado  += CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PAGADA')->sum('valor_cuota');
            $valorPendienteCreditosTotalizado  += CreditFolderDetail::where('code_folder_header', $valCredito->code)->where('status', 'PENDIENTE')->sum('valor_cuota');
        }
        foreach ($creditosTotalizado  as $val) {
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
        //fin totalizado
        $letrasImpagasTotal = CreditFolderDetail::join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->where('credit_folder_details.date_vencimiento', '>=', $inicio)
            ->where('credit_folder_details.date_vencimiento', '<=', $fin)
            ->where('credit_folder_details.status', 'PENDIENTE')
            ->where('credit_folder_headers.status', 'ENTREGADO')
            ->select('credit_folder_details.*', 'credit_folder_details.valor_cuota as valor_cuota_letra', 'credit_folder_headers.code as header_code', 'credit_folder_headers.status as header_status');
        $sumaTotalValorLetra = 0;
        $interesCalculadoLetras = 0;
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
            ->where(function ($query) {
                $query->where('credit_folder_headers.customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('credit_folder_headers.customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->select('credit_folder_details.*', 'credit_folder_details.valor_cuota as valor_cuota_letra', 'credit_folder_headers.code as header_code', 'credit_folder_headers.status as header_status')
            ->paginate(100);

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
        if ($this->tipo_reporte != 0) {
            $dataExport = [
                'tipo_reporte' => $this->tipo_reporte,
                'inicio' => $this->fecha_inicio,
                'fin' => $this->fecha_fin,
                'search' => $this->search,
            ];
            $this->exportar =  $dataExport;
        }
        return view(
            'livewire.reporte-creditos.reporte-creditos-component',
            compact(
                'creditos',
                'letrasImpagas',
                'company',
                'creditosTotal',
                'valorInteresesCreditos',
                'valorPagadoCreditos',
                'valorPendienteCreditos',
                'letrasImpagasTotal',
                'interesCalculadoLetras',
                'sumaTotalValorLetra',
                'valorvalorCuotaTotal',
                'sumaValorSolicitado',
                'creditosTotalizado',
                'sumaValorSolicitadoTotalizado',
                'valorInteresesCreditosTotalizado',
                'valorvalorCuotaTotalTotalizado',
                'valorPagadoCreditosTotalizado',
                'valorPendienteCreditosTotalizado'
            )
        );
    }
}
