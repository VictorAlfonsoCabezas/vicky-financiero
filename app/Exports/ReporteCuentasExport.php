<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerHistorial;
use App\Models\CustomerTipoAhorros;
use App\Models\TipoAhorros;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class ReporteCuentasExport implements FromView
{
    /**
     * @return \Illuminate\Contracts\View\View
     */

    protected $variable;
    protected $search;

    public function __construct($variable, $search)
    {
        $this->variable = $variable;
        $this->search = $search;
    }
    public function view(): View
    {
        if ($this->variable == 0) {

            $cuentas = CustomerTipoAhorros::join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
                ->select(
                    'customer_tipo_ahorros.id',
                    'customer_tipo_ahorros.tipo_ahorros_id',
                    'customer_tipo_ahorros.codigo',
                    'customer_tipo_ahorros.created_at',
                    'customer_tipo_ahorros.customer_id',

                )
                ->where('customer_tipo_ahorros.status', 1)
                ->where(function ($query) {
                    $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
                })
                ->orderBy('customer_tipo_ahorros.codigo', 'asc')
                ->get();
        } else {
            $cuentas = CustomerTipoAhorros::join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
                ->select(
                    'customer_tipo_ahorros.id',
                    'customer_tipo_ahorros.tipo_ahorros_id',
                    'customer_tipo_ahorros.codigo',
                    'customer_tipo_ahorros.created_at',
                    'customer_tipo_ahorros.customer_id',

                )
                ->where('customer_tipo_ahorros.status', 1)
                ->where('tipo_ahorros_id', $this->variable)
                ->where(function ($query) {
                    $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
                })
                ->orderBy('customer_tipo_ahorros.codigo', 'asc')
                ->get();
        }
        foreach ($cuentas as $key => $cuent) {
            $ingresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC'])
                ->where('customer_historials.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_historials.status', true)
                ->sum('customer_historials.valor_movimiento');

            $egresos = CustomerHistorial::join('type_transactions', 'customer_historials.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND'])
                ->where('customer_historials.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_historials.status', true)
                ->sum('customer_historials.valor_movimiento');

            $valor = $ingresos - $egresos;
            $tipoAhorro = TipoAhorros::find($cuent->tipo_ahorros_id);
            $cuent->tipoAhorro = $tipoAhorro->name;
            $customer = Customer::find($cuent->customer_id);
            $cuent->cliente = ((isset($customer->apellidos)) ? $customer->apellidos : '') . ' ' . ((isset($customer->nombres)) ? $customer->nombres : '');
            $cuent->numero_documento = isset($customer->numero_documento) ? $customer->numero_documento : '';
            $cuent->saldo = number_format(round($valor, 2), 2, '.', '');
        }

        $company = Company::find(Auth::user()->company_id);
        return view('reportes.export_cuentas')->with('cuentas', $cuentas)->with('company', $company);
    }
}
