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
    public $search = '';
    public $actionFiltro = '';
    public $afectaFiltro = '';
    public $estadoFiltro = '';

    public function updated($property)
    {
        if (in_array($property, ['search', 'actionFiltro', 'afectaFiltro', 'estadoFiltro'], true)) {
            $this->resetPage();
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'actionFiltro', 'afectaFiltro', 'estadoFiltro']);
        $this->resetPage();
    }

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
            $transaciones = TypeTransaction::where('company_id', Auth::user()->company_id)->findOrFail($this->id_seleccionado);
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
        $this->resetValidation();
        $tipoTans = TypeTransaction::where('company_id', Auth::user()->company_id)->findOrFail($id);
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
        $typetransaction = TypeTransaction::where('company_id', Auth::user()->company_id)
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', $search)
                        ->orWhere('name_corto', 'like', $search)
                        ->orWhere('nombre_cartola', 'like', $search)
                        ->orWhere('description', 'like', $search);
                });
            })
            ->when($this->actionFiltro !== '', function ($query) {
                $query->where('action', $this->actionFiltro);
            })
            ->when($this->afectaFiltro !== '', function ($query) {
                $query->where('afecta', $this->afectaFiltro);
            })
            ->when($this->estadoFiltro !== '', function ($query) {
                $query->where('status', $this->estadoFiltro);
            })
            ->orderBy('name')->orderBy('id')->paginate(15);
        return view('livewire.type-transactions.type-transactions-component', compact('typetransaction'));
    }
}
