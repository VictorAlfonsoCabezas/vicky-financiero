<?php

namespace App\Http\Livewire\CustomerTipoAhorros;

use App\Models\CustomerTipoAhorros;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTipoAhorrosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search;
    public function render()
    {
        $customerTipoAhorros = CustomerTipoAhorros::select(
            'customer_tipo_ahorros.*',
            DB::raw("CONCAT(customer.nombres, ' ', customer.apellidos) as full_name"),
            'customer.numero_documento',
            'tipo_ahorros.name as tipo_ahorros_name',
        )
            ->join('customer', 'customer.id', '=', 'customer_tipo_ahorros.customer_id')
            ->join('tipo_ahorros', 'tipo_ahorros.id', '=', 'customer_tipo_ahorros.tipo_ahorros_id')
            ->where('customer_tipo_ahorros.company_id', Auth::user()->company_id)
            ->where('customer.numero_documento', 'like', '%' . $this->search . '%')
            ->orwhere('customer.nombres', 'like', '%' . $this->search . '%')
            ->orwhere('customer.apellidos', 'like', '%' . $this->search . '%')
            ->orwhere('customer_tipo_ahorros.codigo', 'like', '%' . $this->search . '%')
            ->orderBy('customer_tipo_ahorros.created_at', 'desc')
            ->paginate(10);
        return view('livewire.customer-tipo-ahorros.customer-tipo-ahorros-component', compact('customerTipoAhorros'));
    }
}
