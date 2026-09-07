<?php

namespace App\Exports\ReporteCarteraNiveles;

use App\Http\Controllers\Base\BaseController;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class ReporteCarteranivelesExport implements FromView
{


    public $fecha_inicio;
    public $fecha_fin;

    public function __construct($fecha_inicio, $fecha_fin)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;

    }
    public function view(): View
    {
        $company = Company::find(Auth::user()->company_id);
        $letrasVencidas = CreditFolderDetail::where('date_vencimiento', '>=', $this->fecha_inicio)
            ->where('date_vencimiento', '<=', $this->fecha_fin)
            ->where('status', 'PENDIENTE')
            ->get();
            foreach ($letrasVencidas as $val) {
                $interesMora = BaseController::calculoInteresMoraLetraValorMensualValores($val->id);
                $val->diasInteres = $interesMora['diasInteres'];
                $val->valorInteres = $interesMora['valorInteres'];
                $cabecera = CreditFolderHeader::where('code', $val->code_folder_header)->first();
                if ($cabecera) {
                    $val->customer_id = $cabecera->customer_id;
                    $val->nombreCliente = $cabecera->customer_name;
                    $val->cedulaCliente = $cabecera->customer_ruc;
                    $customer = Customer::find($cabecera->customer_id);
                    if ($customer) {
                        $val->correoCliente = $customer->correo;
                        $val->direccionCliente = $customer->direccion;
                    }
                }
            }
          
        return view('reportes.reporte-cartera-niveles')
            ->with('company', $company)
            ->with('letrasVencidas', $letrasVencidas);
    }
}
