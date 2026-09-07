<?php

namespace App\Http\Livewire\CustomerCuentas;

use App\Models\CustomerTipoAhorros;
use App\Models\Customer;
use App\Models\CustomerHistorial;
use App\Models\TipoAhorrosProgramadosDetalle;
use App\Models\CustomerMovimiento;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Carbon\Carbon;

class CustomerCuentasComponent extends Component
{

    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';


    public $cuenta_selec = 0;
    public $fechaInicio = '';
    public $fechaFin = '';




    public function cuentaSeleccionada($id)
    {
        $this->cuenta_selec = $id;
    }
    public function convertirFecha($fecha)
    {
        $carbonFecha = Carbon::createFromFormat('Y-m-d', $fecha);
        $carbonFecha->locale('es');
        $fechaFormateada = $carbonFecha->isoFormat('dddd, D [de] MMMM [del] YYYY');
        return $fechaFormateada;
    }

    public function obtenerDatos()
    {
        $this->render();
    }

    public function render()
    {

        if ($this->fechaInicio == '') {
            $this->fechaInicio  = date('Y') . '-01-01';
        } else {
            $this->fechaInicio  = $this->fechaInicio;
        }

        if ($this->fechaFin == '') {
            $this->fechaFin  = date('Y-m-d');
        } else {
            $this->fechaFin  = $this->fechaFin;
        }
        $fechaInicio =  $this->fechaInicio;
        $fechaFin =  $this->fechaFin;
        //dd($this->fechaInicio , $this->fechaFin );
        $usuario = User::find(Auth::user()->id);
        $cliente = Customer::where('user_id', Auth::user()->id)->first();
        $cuentas = CustomerTipoAhorros::where('customer_id', $cliente->id)->get();
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
            $cuent->saldo = number_format(round($valor, 2), 2, '.', '');
            $cuent->porcentaje = '';
            if ($cuent->tipo_ahorros_programados_detalle_id) {
                $tipoAhorroDetalle = TipoAhorrosProgramadosDetalle::find($cuent->tipo_ahorros_programados_detalle_id);
                if ($tipoAhorroDetalle != null) {
                    $cuent->porcentaje = $tipoAhorroDetalle->interes;
                }
            }
        }

        if (isset($cliente->code)) {
            if ($this->cuenta_selec > 0) {
                $detalle = CustomerMovimiento::select('customer_movimientos.*', 'type_transactions.name_corto')
                    ->join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                    ->where('customer_movimientos.customer_code', $cliente->code)
                    ->where('customer_movimientos.company_id', Auth::user()->company_id)
                    ->whereIn('type_transactions.name_corto', ['IN', 'EG', 'SE', 'SC', 'DEA', 'SOL', 'CVN', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'DND'])
                    ->where('customer_movimientos.customer_tipo_ahorro_id', $this->cuenta_selec)
                    ->where('customer_movimientos.status', true)
                    //->orderBy('customer_movimientos.id', 'asc')
                    //->select('*', DB::raw('(SELECT COUNT(*) FROM wha_envios WHERE wha_envios.proviene_id = customer_movimientos.id AND wha_envios.type_transacction_id = customer_movimientos.type_transaction_id) AS total'))
                    ->get();
                $valoTotal = 0;
                foreach ($detalle as $valdet) {
                    if ($valdet->name_corto != 'SE') {
                        if ($valdet->type_transaction_action == 'S') {
                            $valoTotal += $valdet->valor_movimiento;
                        } else {
                            $valoTotal -= $valdet->valor_movimiento;
                        }
                    }
                    $valdet->saldoValor = $valoTotal;
                }
            } else {
                $detalle = new CustomerHistorial();
            }
        } else {
            $detalle = new CustomerHistorial();
        }

        return view('livewire.customer-cuentas.customer-cuentas-component', compact('cliente', 'cuentas', 'detalle', 'fechaInicio', 'fechaFin'));
    }
}
