<?php

namespace App\Http\Livewire\CentroCostos;

use App\Models\CentroCostos;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CentroCostosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $code = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $centro = CentroCostos::find($id);
            $this->nombre = $centro->name;
            $this->descripcion = $centro->descripcion;
            $this->code = $centro->code;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeConceptos()
    {
        $this->validate([
            'nombre' => 'required',
        ]);

        if ($this->id_seleccionado > 0) {
            $centro = CentroCostos::find($this->id_seleccionado);
        } else {
            $centro = new CentroCostos();
            $centro->company_id = Auth::user()->company_id;
        }
        $centro->name = $this->nombre;
        $centro->descripcion = $this->descripcion;
        $centro->code = $this->code;
        $centro->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function cambioEstado($id)
    {
        $centro = CentroCostos::find($id);
        $centro->status = !$centro->status;
        $centro->save();
    }

    public function render()
    {
        $centroCostos = CentroCostos::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.centro-costos.centro-costos-component', compact('centroCostos'));
    }
}
