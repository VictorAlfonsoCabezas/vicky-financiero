<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Models\Customer;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use PDF;

class CustomerHistorialController extends Controller
{

    public static function guardarHistorialAutomatica($code, $transaction, $valor, $aquien, $saldoGeneral, $fecha, $customer_tipo_ahorro_id = null, $cargaMasiva = 0)
    {
        $transactionCompleta = TypeTransaction::find($transaction);
        $datas = [
            "company_id" => Auth::user()->company_id,
            "customer_code" => $aquien,
            "afecta" => $transactionCompleta->afecta,
            "customer_movimiento_code" => $code,
            "valor_movimiento" => $valor,
            "saldo_general" => $saldoGeneral,
            "customer_tipo_ahorro_id" => $customer_tipo_ahorro_id,
            "type_transaction_id" => $transactionCompleta->id,
            "type_transaction_name" => $transactionCompleta->name,
            "type_transaction_action" => $transactionCompleta->action,
            "date_created" => $fecha,
            "hour_created" => date("H:i:s"),
            "carga_masiva" => $cargaMasiva,
        ];
        CustomerHistorial::create($datas);
    }
    public static function guardarHistorialAutomaticaCron($code, $transaction, $valor, $aquien, $saldoGeneral, $fecha, $customer_tipo_ahorro_id = null)
    {
        $transactionCompleta = TypeTransaction::find($transaction);
        $datas = [
            "company_id" => 1,
            "customer_code" => $aquien,
            "afecta" => $transactionCompleta->afecta,
            "customer_movimiento_code" => $code,
            "valor_movimiento" => $valor,
            "saldo_general" => $saldoGeneral,
            "customer_tipo_ahorro_id" => $customer_tipo_ahorro_id,
            "type_transaction_id" => $transactionCompleta->id,
            "type_transaction_name" => $transactionCompleta->name,
            "type_transaction_action" => $transactionCompleta->action,
            "date_created" => $fecha,
            "hour_created" => date("H:i:s"),
        ];
        CustomerHistorial::create($datas);
    }

    public function transaccionesMovimientos($id)
    {
        $movimiento = CustomerMovimiento::find($id);
        $data['movimiento'] = $movimiento;
        $data['historial'] = CustomerHistorial::where('company_id', Auth::user()->company_id)
            ->where('customer_movimiento_code', $movimiento->code)
            ->where('customer_code', $movimiento->customer_code)
            ->first();

        $variebleTipo = $data['historial']->type_transaction_name;
        $data['typo'] = '';
        
        if ($variebleTipo  == 'INGRESOS' || $variebleTipo  == 'SUMA CLIENTE') {
            $data['typo'] = 'DEPOSITO';
        } elseif ($variebleTipo  == 'SUMA EMPRESA') {
            $data['typo'] = 'GASTOADMINISTRATIVO';
        } else {
            $data['typo'] = 'RETIRO';
        }
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        return PDF::loadView('reportes.transacciones', compact('data'))
            ->setPaper('A6', "portrait")
            ->download($data['typo'] . ' ' . $data['movimiento']->customer_name . '.pdf');
    }
}
