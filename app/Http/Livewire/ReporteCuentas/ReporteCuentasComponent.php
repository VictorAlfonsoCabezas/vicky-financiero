<?php

namespace App\Http\Livewire\ReporteCuentas;

use App\Models\Customer;
use App\Models\CustomerHistorial;
use App\Models\CustomerTipoAhorros;
use App\Models\TipoAhorros;
use App\Models\TipoAhorrosProgramadosDetalle;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Exports\ReporteCuentasExport;
use App\Models\CustomerMovimiento;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\FormasPago;
use App\Models\Bancos;

class ReporteCuentasComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $historialCuenrtas = [];
    public $tipo_cuenta = 0;
    public $search;
    public $cuentaSeleccionada;

    public function export()
    {
        return Excel::download(new ReporteCuentasExport($this->tipo_cuenta, $this->search), 'cuentas de ahorros.xlsx');
    }
    public function obtenerDatos()
    {
        $this->tipo_cuenta = $this->tipo_cuenta ?? '';
    }

    public function render()
    {
        $tipoAhorros = TipoAhorros::all();
        if ($this->tipo_cuenta == 0) {

            $cuentasQuery = CustomerTipoAhorros::join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
                ->select(
                    'customer_tipo_ahorros.id',
                    'customer_tipo_ahorros.tipo_ahorros_id',
                    'customer_tipo_ahorros.codigo',
                    'customer_tipo_ahorros.created_at',
                    'customer_tipo_ahorros.customer_id',
                    'customer_tipo_ahorros.status'


                )
                ->where(function ($query) {
                    $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
                })
                ->orderBy('customer_tipo_ahorros.codigo', 'asc');
        } else {
            $cuentasQuery = CustomerTipoAhorros::join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
                ->select(
                    'customer_tipo_ahorros.id',
                    'customer_tipo_ahorros.tipo_ahorros_id',
                    'customer_tipo_ahorros.codigo',
                    'customer_tipo_ahorros.created_at',
                    'customer_tipo_ahorros.customer_id',
                    'customer_tipo_ahorros.status'


                )
                ->where('tipo_ahorros_id', $this->tipo_cuenta)
                ->where(function ($query) {
                    $query->where('customer.nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('customer.numero_documento', 'like', '%' . $this->search . '%');
                })
                ->orderBy('customer_tipo_ahorros.codigo', 'asc');
        }
        //dd($cuentasQuery->get());

        /*
        $cuentas = $cuentasQuery->get();
        foreach ($cuentas as $key => $cuent) {

            $ingresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'EUS'])
                ->where('customer_movimientos.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_movimientos.status', true)
                ->sum('customer_movimientos.valor_movimiento');

            $egresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND', 'DCT'])
                ->where('customer_movimientos.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_movimientos.status', true)
                ->sum('customer_movimientos.valor_movimiento');

            $valor = $ingresos - $egresos;
            $tipoAhorro = TipoAhorros::find($cuent->tipo_ahorros_id);
            $cuent->tipoAhorro = $tipoAhorro->name;
            $customer = Customer::find($cuent->customer_id);
            $cuent->cliente = (isset($customer->apellidos) ? $customer->apellidos : '') . ' ' . (isset($customer->nombres) ? $customer->nombres : '');
            $cuent->numero_documento = isset($customer->numero_documento) ? $customer->numero_documento : '';
            $cuent->saldo = number_format(round($valor, 2), 2, '.', '');
        }
*/
        // Realizar la paginación después de modificar las cuentas
        $cuentasPaginadas = $cuentasQuery->paginate(15);

        foreach ($cuentasPaginadas as $key => $cuent) {
            $ingresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['IN', 'SC', 'SOL', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'EUS'])
                ->where('customer_movimientos.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_movimientos.status', true)
                ->sum('customer_movimientos.valor_movimiento');

            $egresos = CustomerMovimiento::join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                ->whereIn('type_transactions.name_corto', ['EG', 'DEA', 'CVN', 'DND', 'DCT'])
                ->where('customer_movimientos.customer_tipo_ahorro_id', $cuent->id)
                ->where('customer_movimientos.status', true)
                ->sum('customer_movimientos.valor_movimiento');

            $valor = $ingresos - $egresos;
            //$cuentasQuery = CustomerTipoAhorros::with(['customer', 'tipoAhorro']);
            //$cuent->customer->nombres;
            //$cuent->tipoAhorro->name;
            $tipoAhorro = TipoAhorros::find($cuent->tipo_ahorros_id);
            $cuent->tipoAhorro = $tipoAhorro->name;
            $customer = Customer::find($cuent->customer_id);
            $cuent->cliente = (isset($customer->apellidos) ? $customer->apellidos : '') . ' ' . (isset($customer->nombres) ? $customer->nombres : '');
            $cuent->numero_documento = isset($customer->numero_documento) ? $customer->numero_documento : '';
            $cuent->saldo = number_format(round($valor, 2), 2, '.', '');
        }

        return view('livewire.reporte-cuentas.reporte-cuentas-component', [
            'cuentas' => $cuentasPaginadas,
            'tipoAhorros' => $tipoAhorros
        ]);
    }

    public function verCuenta($id)
    {
        $cuenta = CustomerTipoAhorros::find($id);

        $customer = Customer::find($cuenta->customer_id);
        $tipoAhorro = TipoAhorros::find($cuenta->tipo_ahorros_id);

        $cuenta->cliente = $customer->apellidos . ' ' . $customer->nombres;
        $cuenta->numero_documento = $customer->numero_documento;
        $cuenta->tipoAhorro = $tipoAhorro->name;

        $ingresos = CustomerMovimiento::where('customer_tipo_ahorro_id', $cuenta->id)->sum('valor_movimiento');
        $cuenta->saldo = $ingresos;


        //nuevo
        $formaPago = FormasPago::find($cuenta->forma_pago_id);

        $cuenta->forma_pago_nombre = $formaPago ? $formaPago->nombre : 'N/A';

        $banco = Bancos::find($cuenta->banco_id);

        $cuenta->banco_nombre = $banco ? $banco->nombre : '';

        $this->cuentaSeleccionada = $cuenta;

        $this->dispatchBrowserEvent('show-modal-cuenta');
    }

    public function aprobarCuenta($id)
    {
        $cuenta = CustomerTipoAhorros::find($id);

        $cuenta->status = 1;
        $cuenta->save();

        $this->dispatchBrowserEvent('alerta', [
            'titulo' => 'Éxito',
            'color' => 'success',
            'mensaje' => 'Cuenta aprobada correctamente'
        ]);

        $this->dispatchBrowserEvent('close-modal');
    }
}
