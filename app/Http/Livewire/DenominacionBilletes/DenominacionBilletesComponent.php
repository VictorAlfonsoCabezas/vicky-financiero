<?php

namespace App\Http\Livewire\DenominacionBilletes;

use App\DenominacionBilletes;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class DenominacionBilletesComponent extends Component
{

    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['pagarnovacion'];

    public $id_seleccionado = 0;
    public $nombre = '';
    public $valor = '';


    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
        $this->render();
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre','valor']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        if($this->id_seleccionado > 0){
            $billetes = DenominacionBilletes::find($this->id_seleccionado);
        }else{
            $billetes = new DenominacionBilletes();
        }
        $billetes->company_id = Auth::user()->company_id;
        $billetes->nombre = $this->nombre;
        $billetes->valor = $this->valor;
        $billetes->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarBilletes($id)
    {
        DenominacionBilletes::find($id)->delete();
    }

    public function editarBilletes($id)
    {
        $this->id_seleccionado = $id;
        $billetes = DenominacionBilletes::find($id);
        $this->nombre = $billetes->nombre;
        $this->valor = $billetes->valor;
    }

    protected $rules = [
        'nombre' => 'required',
        'valor' => 'required',
    ];
    
    public function render()
    {
        $billetes = DenominacionBilletes::where('company_id',Auth::user()->company_id)->paginate(10);
        return view('livewire.denominacion-billetes.denominacion-billetes-component', compact('billetes'));
    }
    
}
