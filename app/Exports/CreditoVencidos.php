<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DateTime;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CreditoVencidos implements FromView
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
        //    dd('vencidos', $this->data);
        $fechaActual = date('Y-m-d');
        $datos = $this->data;
        $inicio = $datos['inicio'];
        $fin = $datos['fin'];
        $creditos = CreditFolderDetail::where('date_vencimiento', '>=', $inicio)->where('date_vencimiento', '<=', $fechaActual)->where('status', 'PENDIENTE')->get();
        foreach ($creditos as $val) {
            $val->customerName = CreditFolderHeader::where('code', $val->code_folder_header)->first()->customer_name;
            $val->direccion = CreditFolderHeader::where('code', $val->code_folder_header)->first()->customer_address;
            $val->telefono = CreditFolderHeader::where('code', $val->code_folder_header)->first()->customer_phone;
            $val->nombreGarante = CreditFolderHeader::where('code', $val->code_folder_header)->first()->customer_garante_name;
            $garanteID = CreditFolderHeader::where('code', $val->code_folder_header)->first()->customer_garante_id;
            $garanteValores = Customer::find($garanteID);
            $val->ciGarante = (isset($garanteValores->numero_documento)) ?   $garanteValores->numero_documento  : '';
            $val->telefonoGarante =  (isset($garanteValores->telefono)) ?   $garanteValores->telefono  : '';
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
        $company = Company::find(Auth::user()->company_id);
        return view('reportes.export_creditos_vencidos')->with('creditos', $creditos)->with('company', $company);
    }
}
