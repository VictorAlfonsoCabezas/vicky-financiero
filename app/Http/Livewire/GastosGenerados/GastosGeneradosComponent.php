<?php

namespace App\Http\Livewire\GastosGenerados;

use App\Models\CustomerMovimiento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GastosGeneradosComponent extends Component
{

    public $id_seleccionado = 0;
    public $code = '';    
    public $customer_name  = '';
    public $type_transaction_name = '';
    public $observation = '';
    public $saldo_general = '';


    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        
    }

    public function render()
    {
        $gastos = CustomerMovimiento ::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.gastos-generados.gastos-generados-component',compact('gastos'));
    }
}
