<?php

namespace App\Http\Livewire\ReporteCarteraNiveles;

use App\Exports\ReporteCarteraNiveles\ReporteCarteranivelesExport;
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


class ReporteCarteraNivelesComponent extends Component
{

    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';


    public $fecha_inicio = '';
    public $fecha_fin = '';



    public function mount()
    {
        $this->fecha_inicio = date('Y-m-d');
        $this->fecha_fin = date('Y-m-d');

    }

    public function export()
    {
        if ($this->fecha_inicio  != '' and $this->fecha_fin != '') {

            return Excel::download(new ReporteCarteranivelesExport($this->fecha_inicio, $this->fecha_fin), 'Reporte Cartera fecha de corte del ' . $this->fecha_inicio . ' al ' . $this->fecha_fin . '.xlsx');
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
        $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
            ->where('date_vencimiento', '<=', $this->fecha_fin)
            ->where('status', 'PENDIENTE')
            ->paginate(15);
           // ->get();
        foreach ($letrasVencidas as $val) {
            $interesMora =  BaseController::calculoInteresMoraLetraValorMensualValores($val->id);
            $val->diasInteres = $interesMora['diasInteres'];
            $val->valorInteres = $interesMora['valorInteres'];
            $cabecera = CreditFolderHeader::where('code', $val->code_folder_header)->first();
            if ($cabecera) {
                $val->customer_id = $cabecera->customer_id;
                $val->nombreCliente = $cabecera->customer_name;
                $val->cedulaCliente = $cabecera->customer_ruc;
                //$val->celularCliente = $cabecera->customer_phone;
                $customer = Customer::find($cabecera->customer_id);
                if($customer){
                    $val->correoCliente = $customer->correo;
                    $val->direccionCliente = $customer->direccion;
                    $val->celularCliente = $customer->telefono;
                }
            }
        }
        return view('livewire.reporte-cartera-niveles.reporte-cartera-niveles-component', compact('letrasVencidas'));
    }
}
