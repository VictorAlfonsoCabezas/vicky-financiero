<?php

namespace App\Http\Livewire\TipoCustomer;

use App\Models\TipoCustomer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TipoCustomerComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';
    public $socio = false;
    public $particular = false;

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'socio', 'particular']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        if($this->id_seleccionado > 0){
            $cliente = TipoCustomer::find($this->id_seleccionado);
        }else{
            $cliente = new TipoCustomer();
        }
        $cliente->company_id = Auth::user()->company_id;
        $cliente->nombre = $this->nombre;
        $cliente->socio= $this->socio;
        $cliente->particular= $this->particular;
        $cliente->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarCustomer($id)
    {
        TipoCustomer::find($id)->delete();
    }
    
    public function editarCustomer($id)
    {
        $this->id_seleccionado = $id;
        $cliente = TipoCustomer::find($id);
        $this->nombre = $cliente->nombre;  
    }

    public function cambioEstado($id)
    {
        $cliente = TipoCustomer::find($id);
        $cliente->status = ($cliente->status) ? false : true;
        $cliente->save();
    }

    protected $rules = [
        'nombre' => 'required',
        'socio' => 'required',  
        'particular' => 'required',
    ];

    public function render()
    {
        $customer = TipoCustomer::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.tipo-customer.tipo-customer-component', compact('customer'));
    }
}
