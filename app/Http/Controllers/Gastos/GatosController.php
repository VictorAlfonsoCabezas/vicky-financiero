<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Fondo\FondoController;
use App\Models\Bancos;
use App\Models\CentroCostos;
use App\Models\CustomerMovimiento;
use App\Models\Customer;
use App\Models\FormasPago;
use App\Models\PlanCuentas;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Customer\CustomerHistorialController;
use App\Http\Controllers\Cajas\CajasController;
use App\Models\Cajas;
use App\Models\Company;
use Response;
use PDF;

class GatosController extends Controller
{

    public function index()
    {

        $formasPago = FormasPago::where('status', true)->get()->toArray();

        $planCuentas = PlanCuentas::where('company_id', Auth::user()->company_id)->where('gasto', true)->where('status', true)->get()->toArray();

        $centroCostos = CentroCostos::where('company_id', Auth::user()->company_id)->where('status', true)->get()->toArray();

        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'GAS')
            ->first();
        $gastos = CustomerMovimiento::where('afecta', 'E')
            ->where('type_transaction_id', $transaction->id)
            ->where('status', true)
            ->where('company_id', Auth::user()->company_id)
            ->get();


        $mostrarTransferencia = Bancos::where('tipo_cuenta_id', [1, 2])
            ->where('status', 1)
            ->orderBy('nombre')
            ->get();
        $bancos = Bancos::whereIn('tipo_cuenta_id', [1, 2])
            ->where('status', 1)
            ->orderBy('nombre')
            ->get();
        //dd($bancos );




        foreach ($gastos as $val) {
            $formaPago = FormasPago::find($val->id_forma_Pago);
            $val->FormaPago = ($formaPago) ? $formaPago->nombre : '';

            $val->PlanCuenta = optional(
                PlanCuentas::find($val->gastos_plan_cuentas_id)
            )->nombre ?? '';

            $val->CentroCosto = optional(
                CentroCostos::find($val->gastos_centro_costos_id)
            )->name ?? '';
            $banco = Bancos::find($val->banco_id);
            $val->bancoNombre = '';
            if ($banco) {
                $val->bancoNombre = $banco->nombre;

            }

        }
        $sumaGastos = CustomerMovimiento::where('afecta', 'E')
            ->where('type_transaction_id', $transaction->id)
            ->where('status', true)
            ->where('company_id', Auth::user()->company_id)
            ->sum('valor_movimiento');
        return view('gastos/index')
            ->with('sumaGastos', $sumaGastos)
            ->with('formasPago', $formasPago)
            ->with('gastos', $gastos)
            ->with('centroCostos', $centroCostos)
            ->with('planCuentas', $planCuentas)
            ->with('bancos', $bancos)
            ->with('mostrarTransferencia', $mostrarTransferencia);
    }

    public function store(Request $request)
    {

        //        CajasController::abrirCajaAutomatica();
        $customer = Customer::find(1);
        $company = Company::find(Auth::user()->company_id);
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->where('name_corto', 'GAS')
            ->first();
        $tabla = 'customer_movimientos';
        $code = BaseController::generarCodigo($tabla, 9);
        $valorTotal = BaseController::valorTotal();
        $formaPago = FormasPago::find($request->input('forma_pago_id'));
        $data = [
            "code" => $code,
            "company_id" => $company->id,
            "customer_code" => $company->id,
            "afecta" => $transaction->afecta,
            "customer_name" => $company->company_name,
            "customer_ruc" => $company->ruc,
            "customer_address" => $company->address,
            "customer_telefono" => $company->phone,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor_movimiento" => $request->input('valor_gasto'),
            "saldo_general" => $valorTotal - $request->input('valor_gasto'),
            "observation" => strtoupper($request->input('razon_gasto')),
            "user_created_id" => Auth::user()->id,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "id_forma_Pago" => $request->input('forma_pago_id'),
            "forma_pago_id" => $formaPago->id,
            "forma_pago_name" => $formaPago->nombre,
            "gastos_plan_cuentas_id" => $request->input('gastos_plan_cuentas_id'),
            "gastos_centro_costos_id" => $request->input('gastos_centro_costos_id'),
            "banco_id" => $request->input('banco_id'),
            "comprobante" => $request->input('numero_comprobante'),
        ];
        $movimientos = CustomerMovimiento::create($data);
        //        CajasController::calcularCajaAutomatica();
        CustomerHistorialController::guardarHistorialAutomatica($movimientos->code, $transaction->id, $request->input('valor_gasto'), $company->id, $movimientos->saldo_general, date('Y-m-d'));
        return Response::json(true);
    }

    public function pdfGastos($id)
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['datos'] = CustomerMovimiento::find($id);
        $data['company'] = $company;
        return PDF::loadView('reportes.gasto', compact('data'))
            ->setPaper('A6', "portrait")
            ->download('Gasto - Fecha- ' . $data['datos']->date_created . '.pdf');
    }

    public function pdfGastosNuevo($id)
    {
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['datos'] = CustomerMovimiento::where('gastos_id', $id)->first();
        $data['company'] = $company;
        return PDF::loadView('reportes.gasto', compact('data'))
            ->setPaper('A6', "portrait")
            ->download('Gasto - Fecha- ' . $data['datos']->date_created . '.pdf');
    }
    /*

        public $bancos = [];
        public $banco_id;
        public $numero_comprobante;
        public $mostrarTransferencia = false;

        public function updatedFormaPagoId($value)
        {
            $this->mostrarTransferencia = ($value == 3);
            if ($this->mostrarTransferencia) {
                $this->bancos = Bancos::whereIn('tipo_cuenta_id', [1, 2])
                    ->where('status', 1)
                    ->orderBy('nombre')
                    ->get();
            } else {
                $this->bancos = [];
            }
        }

        public function banco()
        {
            return $this->belongsTo(\App\Models\Bancos::class, 'banco_id');
        }*/


}
