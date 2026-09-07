<?php

namespace App\Http\Livewire\ReporteIngresos;

use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\FormasPago;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteIngresos\ReporteIngresosExport;

class ReporteIngresosComponent extends Component
{

    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';


    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $search = '';

    public function mount()
    {
        $this->fecha_inicio = date('Y-m-d');
        $this->fecha_fin = date('Y-m-d');
    }

    public function export()
    {
        if ($this->fecha_inicio  != '' and $this->fecha_fin != '') {

            return Excel::download(new ReporteIngresosExport($this->fecha_inicio, $this->fecha_fin, $this->search), 'Reporte de Ingresos del  ' . $this->fecha_inicio . ' al ' . $this->fecha_fin . '.xlsx');
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
        $letrasVencidas = $letrasVencidasPagadas->paginate(50);
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
        return view('livewire.reporte-ingresos.reporte-ingresos-component', compact('letrasVencidas', 'letrasVencidasPagadas', 'componentesSuma', 'gastosCobranzaSumado', 'letrasVencidasPagadasSuma'));
    }
}
