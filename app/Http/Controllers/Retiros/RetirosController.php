<?php

namespace App\Http\Controllers\Retiros;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Models\CustomerMovimiento;
use App\Models\Customer;
use App\Models\TypeTransaction;
use App\Models\CustomerHistorial;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Cartola\CartolaController;
use App\Http\Controllers\Fondo\FondoController;
use App\Http\Controllers\Cajas\CajasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use Response;

class RetirosController extends Controller {

    public function index() {
        $movimientos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
                ->where('type_transaction_id', 2)
                ->get();
        foreach ($movimientos as $value) {
            if ($value->user_cancel_id !== null)
                $value->user_cancel_name = User::find($value->user_cancel_id)->username;
            $value->user_created_name = User::find($value->user_created_id)->username;
        }
        return view('retiros/index')
                        ->with('movimientos', $movimientos);
    }

    public function create() {
        return view('retiros/create');
    }

    public function store(Request $request) {
//        CajasController::abrirCajaAutomatica();
        $customer = Customer::find($request->input('id_customer'));
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'EG')
                ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $data = [
            "code" => $code,
            "company_id" => Auth::user()->company_id,
            "customer_code" => $customer->code,
            "afecta" => $transaction->afecta,
            "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
            "customer_ruc" => $customer->numero_documento,
            "customer_address" => $customer->direccion,
            "customer_telefono" => $customer->telefono,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $request->input('valor_movimiento'),
            "saldo_general" => $valorTotal - $request->input('valor_movimiento'),
            "observation" => $request->input('observation'),
            "user_created_id" => Auth::user()->id,
            "date_created" => $request->input('date_created'),
            "hour_created" => date("H:i:s"),
        ];
        $movimientos = CustomerMovimiento::create($data);
//        CajasController::calcularCajaAutomatica();
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $request->input('valor_movimiento'), $customer->code, $movimientos->saldo_general, $request->input('date_created'));
        CartolaController::ingresoCartola($movimientos->customer_code, $movimientos->type_transaction_id, $movimientos->valor_movimiento, $request->input('date_created'));
        return Response::json($movimientos);
    }

    public function destroy(Request $request, $id) {
        $customerMovimiento = CustomerMovimiento::find($id);
        $customerMovimiento->razon_cancel = strtoupper($request->input('razon_retiro'));
        $customerMovimiento->user_cancel_id = Auth::user()->id;
        $customerMovimiento->date_cancel = date('Y-m-d');
        $customerMovimiento->hour_cancel = date("H:i:s");
        $customerMovimiento->status = false;
        $customerMovimiento->save();
        $customerHistorial = CustomerHistorial::where('customer_movimiento_code', $customerMovimiento->code)->first();
        $customerHistorial->status = false;
        $customerHistorial->save();
        return Response::json(true);
    }

}
