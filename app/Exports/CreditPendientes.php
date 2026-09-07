<?php

namespace App\Exports;

use App\Http\Controllers\Base\BaseController;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DateTime;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreditPendientes implements FromView
{

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function view(): View
    {
        //pendientes
        $company = Company::find(Auth::user()->company_id);
        $datos = $this->data;
        $inicio = $datos['inicio'];
        $fin = $datos['fin'];
        $fechaActual = date('Y-m-d');

        $creditos = CreditFolderDetail::where('date_vencimiento', '>=', $inicio)->where('date_vencimiento', '<=', $fin)->where('status', 'PENDIENTE')->get();
        foreach ($creditos as $val) {
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
        $company = Company::find(Auth::user()->company_id);
        return view('reportes.export_creditos_pendientes')->with('creditos', $creditos)->with('company', $company);
    }
}
