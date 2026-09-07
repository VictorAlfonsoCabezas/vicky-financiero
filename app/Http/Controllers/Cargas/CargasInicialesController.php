<?php

namespace App\Http\Controllers\Cargas;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerTarifa;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CredirDetailExport;
use App\Imports\CredirDetailImport;
use App\Models\Prestamos;
use Response;

class CargasInicialesController extends Controller {

    public function index() {
        $garantes = Customer::all();
        $tarifas = CustomerTarifa::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        return view('cargas/index')
                        ->with('garantes', $garantes)
                        ->with('tarifas', $tarifas);
    }

    public function cargarDetalles(Request $request, $i_customer, $date, $valor, $garante, $carpeta, $valor_encaje) {
        $data = array();
        $xls = $request->file('file');
        $row = Excel::import(new CredirDetailImport, $xls);
        $detalleCuotas = CreditFolderDetail::where('code_folder_header', $carpeta)->count();

        $customer = Customer::find($i_customer);
        $garanteDato = Customer::find($garante);
        $sumaInteres = 0;
        $deuda = $valor;

        $interes = ($customer->customer_tarifa_interes > 0 ) ? round($customer->customer_tarifa_interes, 2) : 1;
        $numCuotas = $detalleCuotas;
        $inte = ($interes / 100);
        $cuotas = ($deuda * $inte * (pow((1 + $inte), (($detalleCuotas))))) / ((pow((1 + $inte), (($detalleCuotas)))) - 1);
        $fondoDesgravamen = number_format((($deuda * (1 / 100)) / $numCuotas), 2);
        $valorCuotas = number_format($cuotas + $fondoDesgravamen, 2);
        $fechaActual = $date;

        $prestamoPrimero =  Prestamos::where('status', 'A')->first();

        $dataHeader = [
            "company_id" => Auth::user()->company_id,
            "code" => $carpeta,
            "customer_id" => $customer->id,
            "customer_code" => $customer->code,
            "customer_ruc" => $customer->numero_documento,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_phone" => $customer->telefono,
            "customer_address" => $customer->direccion,
            "customer_email" => $customer->correo,
            "customer_garante_id" => (isset ($garanteDato->id)) ? $garanteDato->id : 0,
            "customer_garante_name" => (isset ($garanteDato->id)) ? $garanteDato->nombres . ' ' . $garanteDato->apellidos : '',
            "valor_solicitado" => $deuda,
            "anios_pagar" => ($detalleCuotas / 12),
            "cuotas_pagar" => $numCuotas,
            "valor_cuota" => $valorCuotas,
            "valor_desgravamen" => $fondoDesgravamen,
            "date_created" => $date,
            "hour_created" => date("H:i:s"),
            "user_created_id" => Auth::user()->id,
            "user_created_name" => Auth::user()->username,
            "status" => 'ENTREGADO',
            "tipo_prestamo" => $prestamoPrimero->id,
            "encaje_valor" => $valor_encaje,
        ];
        $createHeader = CreditFolderHeader::create($dataHeader);
        return Response::json(true);
    }

    public function descargaDetalles() {
        $file_path = public_path('archivos/detalleCuotas.xlsx');
        return response()->download($file_path);
    }

}
