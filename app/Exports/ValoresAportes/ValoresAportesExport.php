<?php

namespace App\Exports\ValoresAportes;

use App\Models\Company;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use App\Models\Customer;
use App\Models\CustomerTipoAhorros;
use App\Models\TipoAhorros;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;


class ValoresAportesExport implements FromView
{
    protected $variable;

    public function __construct($variable)
    {
        $this->variable = $variable;
    }

    public function view(): View
    {
        $variableConsulta = $this->variable;
        $fin = $variableConsulta['mes_fin_dias'];

        $company = Company::find(Auth::user()->company_id);
        $cuentasDescargoIds = TipoAhorros::where('cuenta_certificado', true)->pluck('id')->toArray();
        $cuentasClientes = CustomerTipoAhorros::whereIn('tipo_ahorros_id', $cuentasDescargoIds)->get();
      
        foreach ($cuentasClientes as $val) {
            $customer =  Customer::find($val->customer_id);
            $cuenta = TipoAhorros::find($val->tipo_ahorros_id);

            $val->cuenta = $cuenta->name;
            $val->cliente = $customer->nombres . ' ' . $customer->apellidos;
            $val->valorperiodico = $cuenta->valor_periodico;
            $val->letrasPendientes = 0;
            $val->valorPendiente = 0;
            $val->letrasVencidas = array();



            if ($fin  != '') {
                $creditos = CreditFolderHeader::where('customer_id', $val->customer_id)->pluck('code')->toArray();
                $valoresLetras = CreditFolderDetail::whereIn('code_folder_header', $creditos)->where('date_vencimiento', '<=', $fin );
                $val->letrasPendientes = $valoresLetras->count();
                $val->valorPendiente = $valoresLetras->sum('valor_cuota');
                $val->letrasVencidas = $valoresLetras->get();
            }
            $val->totalRecaudar =  $val->valorperiodico + $val->valorPendiente;
        }

        return view('reportes.valores-aportes-export') 
        ->with('cuentasClientes', $cuentasClientes)
        ->with('company', $company);;
       
    }
}

