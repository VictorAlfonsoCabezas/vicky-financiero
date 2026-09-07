<?php

namespace App\Http\Livewire\CustomerMovimientos;

use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CustomerTipoAhorros;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomerMovimientosComponent extends Component
{
    public $cuenta = null;
    public $fechaInicio;
    public $fechaFin;

    public function mount()
    {
        $this->fechaInicio = date('Y-m-01');
        $this->fechaFin = date('Y-m-t');
    }

    public function render()
    {
        $cliente = Customer::where('user_id', Auth::user()->id)->first();
        $tipoCuenta = CustomerTipoAhorros::where('customer_id', (isset($cliente->id)) ? $cliente->id : null)->get();
        if ($this->cuenta == null) {
            $cuenta = CustomerTipoAhorros::where('customer_id', (isset($cliente->id)) ? $cliente->id : null)->first();
            $this->cuenta = (isset($cuenta->id)) ? $cuenta->id : null;
        }
        $movimientos = CustomerMovimiento::where('company_id', Auth::user()->company_id)
            ->whereIn('type_transaction_id', [1, 2, 23, 24])
            ->where('customer_code', (isset($cliente->code)) ? $cliente->code : null)
            ->where('customer_tipo_ahorro_id', ($this->cuenta !== null) ? $this->cuenta : null)
            ->whereBetween('date_created', [$this->fechaInicio, $this->fechaFin])
            ->orderByDesc('id')
            ->paginate(10);
        return view('livewire.customer-movimientos.customer-movimientos-component', compact('movimientos', 'tipoCuenta'));
    }
}
