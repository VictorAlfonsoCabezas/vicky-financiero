<?php

namespace App\Exports\ReporteIngresos;

use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\FormasPago;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReporteIngresosExport implements FromView
{
    public $fecha_inicio;
    public $fecha_fin;
    public $search;

    public function __construct($fecha_inicio, $fecha_fin, $search)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->search = $search;
    }
    public function view(): View
    {
        $company = Company::find(Auth::user()->company_id);



        $letrasVencidasPagadasSuma = CreditFolderDetail::join(
            'credit_folder_headers',
            'credit_folder_details.code_folder_header',
            '=',
            'credit_folder_headers.code'
        )
            ->whereBetween('credit_folder_details.date_pay', [$this->fecha_inicio, $this->fecha_fin])
            ->where('credit_folder_details.status', 'PAGADA')
            ->where(function ($query) {
                $query->where('credit_folder_headers.customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('credit_folder_headers.customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->sum('credit_folder_details.valor_cuota');


        $letrasVencidasPagadas = CreditFolderDetail::join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->where('credit_folder_details.date_pay', '>=', $this->fecha_inicio)
            ->where('credit_folder_details.date_pay', '<=', $this->fecha_fin)
            ->where('credit_folder_details.status', 'PAGADA')
            ->where(function ($query) {
                $query->where('credit_folder_headers.customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('credit_folder_headers.customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->select('credit_folder_details.*', 'credit_folder_headers.code as header_code', 'credit_folder_headers.status as header_status');
        $componentesSuma = 0;
        $gastosCobranzaSumado = 0;

        foreach ($letrasVencidasPagadas->get() as $value) {
            $credito  =  CreditFolderHeader::where('code', $value->code_folder_header)->first();
            $componentesSuma += number_format(($credito->gato_administrativo + $credito->tercer_gasto + $credito->segundo_gasto + $credito->primer_gasto) / $credito->cuotas_pagar, 2, '.', '');
            $gastosCobranzaSumado += number_format($value->notificado *  $company->valor_notificado, 2, '.', '');
        }


        $letrasVencidas = CreditFolderDetail::join('credit_folder_headers', 'credit_folder_details.code_folder_header', '=', 'credit_folder_headers.code')
            ->where('credit_folder_details.date_pay', '>=', $this->fecha_inicio)
            ->where('credit_folder_details.date_pay', '<=', $this->fecha_fin)
            ->where('credit_folder_details.status', 'PAGADA')
            ->where(function ($query) {
                $query->where('credit_folder_headers.customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('credit_folder_headers.customer_ruc', 'like', '%' . $this->search . '%');
            })
            ->select('credit_folder_details.*', 'credit_folder_headers.code as header_code', 'credit_folder_headers.status as header_status')
            ->get();
            
        foreach ($letrasVencidas as $val) {
            $credito  =  CreditFolderHeader::where('code', $val->code_folder_header)->first();
            $val->identificacionCliente = '';
            $val->nombreSocio = '';
            $val->idCliente = '';
            $val->formaPago = $val->tipo_pago . '';
            $formaPago =  FormasPago::find($val->tipo_pago);
            $val->gastosComponentes = number_format(($credito->gasto_administrativo + $credito->tercer_gasto + $credito->segundo_gasto + $credito->primer_gasto) / $credito->cuotas_pagar, 2, '.', '');
            $val->gastosCobranza = number_format($val->notificado *  $company->valor_notificado, 2, '.', '');
            if ($formaPago) {
                $val->formaPago = $formaPago->nombre;
            }

            if ($credito) {
                $customerGarante =  Customer::find($credito->customer_id);
                $val->idCliente = $customerGarante->id;
                $val->identificacionCliente = $credito->customer_ruc;
                $val->nombreSocio = $credito->customer_name;
            }
            $customerMovimiento =  CustomerMovimiento::where('credit_folder_details_id', $val->id)->first();
        }

        return view('reportes.reporte-ingresos')
            ->with('company', $company)
            ->with('letrasVencidasPagadasSuma', $letrasVencidasPagadasSuma)
            ->with('componentesSuma', $componentesSuma)
            ->with('gastosCobranzaSumado', $gastosCobranzaSumado)
            ->with('letrasVencidasPagadas', $letrasVencidasPagadas)
            ->with('letrasVencidas',  $letrasVencidas);
    }
}
