<?php

namespace App\Http\Livewire\TypeTransactions;

use App\Models\TypeTransaction;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TypeTransactionsComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $id_seleccionado = 0;
    public $company_id = '';
    public $name = '';
    public $name_corto = '';
    public $nombre_cartola = '';
    public $description = '';
    public $action = '';
    public $afecta = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['name', 'description', 'action', 'afecta', 'name_corto', 'nombre_cartola']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeTransaciones()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $transaciones = TypeTransaction::find($this->id_seleccionado);
        } else {
            $transaciones = new TypeTransaction();
        }
        $transaciones->company_id = Auth::user()->company_id;
        $transaciones->name = $this->name;
        $transaciones->name_corto = $this->name_corto;
        $transaciones->nombre_cartola = $this->nombre_cartola;
        $transaciones->description = $this->description;
        $transaciones->action = $this->action;
        $transaciones->afecta = $this->afecta;
        $transaciones->save();
        $this->dispatchBrowserEvent('closeModal');
    }
    public function consultarDatosEdit($id)
    {
        $tipoTans = TypeTransaction::find($id);
        $this->id_seleccionado = $id;
        $this->name = $tipoTans->name;
        $this->name_corto = $tipoTans->name_corto;
        $this->nombre_cartola = $tipoTans->nombre_cartola;
        $this->description = $tipoTans->description;
        $this->action = $tipoTans->action;
        $this->afecta = $tipoTans->afecta;
    }

    protected $rules = [
        'name' => 'required',
        'description' => 'required',
        'action' => 'required',
        'afecta' => 'required',
    ];

    public function render()
    {
        $typetransaction = TypeTransaction::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.type-transactions.type-transactions-component', compact('typetransaction'));
    }
}
