<?php

namespace App\Http\Livewire\SedesCentroCostos;

use App\Models\CentroCostos;
use App\Models\Sedes;
use App\Models\SedesCentroCostos;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SedesCentroCostosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $id_seleccionado = 0;
    public $sede_id = '';
    public $centro_costos_id = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $sede = SedesCentroCostos::find($id);
            $this->sede_id = $sede->sede_id;
            $this->centro_costos_id = $sede->centro_costos_id;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['sede_id', 'centro_costos_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeConceptos()
    {
        $this->validate([
            'sede_id' => 'required',
            'centro_costos_id' => 'required',
        ]);

        if ($this->id_seleccionado > 0) {
            $sede = SedesCentroCostos::find($this->id_seleccionado);
        } else {
            $sede = new SedesCentroCostos();
            $sede->company_id = Auth::user()->company_id;
        }
        $sede->sede_id = $this->sede_id;
        $sede->centro_costos_id = $this->centro_costos_id;
        $sede->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function render()
    {
        $sedes = Sedes::where('company_id', Auth::user()->company_id)->get();
        $centroCostos = CentroCostos::where('company_id', Auth::user()->company_id)->get();
        $sedesCentroCostos = SedesCentroCostos::select('sedes_centro_costos.*', 'sedes.name as nombresede', 'centro_costos.name as nombrecentro')
            ->join('sedes', 'sedes_centro_costos.sede_id', '=', 'sedes.id')
            ->join('centro_costos', 'sedes_centro_costos.centro_costos_id', '=', 'centro_costos.id')
            ->where('sedes_centro_costos.company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('sedes.name', 'like', '%' . $this->search . '%')
                    ->orWhere('centro_costos.name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.sedes-centro-costos.sedes-centro-costos-component', compact('sedesCentroCostos', 'sedes', 'centroCostos'));
    }
}
