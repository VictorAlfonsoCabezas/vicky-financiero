<?php

namespace App\Http\Livewire\Conceptos;

use App\Models\Conceptos;
use App\Models\TipoConcepto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConceptosComponent extends Component
{

    public $id_seleccionado = 0;
    public $nombre = '';
    public $tipo_concepto_id = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $conceptos = Conceptos::find($id);
            $this->nombre = $conceptos->nombre;
            $this->tipo_concepto_id = $conceptos->tipo_concepto_id;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'tipo_concepto_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeConceptos()
    {
        $this->validate([
            'nombre' => 'required',
            'tipo_concepto_id' => 'required'
        ]);

        if ($this->id_seleccionado > 0) {
            $conceptos = Conceptos::find($this->id_seleccionado);
        } else {
            $conceptos = new Conceptos();
            $conceptos->company_id = Auth::user()->company_id;
        }
        $conceptos->nombre = $this->nombre;
        $conceptos->tipo_concepto_id = $this->tipo_concepto_id;
        $conceptos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarConceptos($id)
    {
        Conceptos::find($id)->delete();
    }

    public function cambioEstado($id)
    {
        $conceptos = Conceptos::find($id);
        $conceptos->status = ($conceptos->status) ? false : true;
        $conceptos->save();
    }

    public function render()
    {
        $tipos = TipoConcepto::where('company_id', Auth::user()->company_id)->get();
        $conceptos = Conceptos::select('conceptos.*', 'tipo_concepto.nombre as concepto_nombre')
            ->join('tipo_concepto', 'conceptos.tipo_concepto_id', '=', 'tipo_concepto.id')
            ->where('conceptos.company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('conceptos.nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('tipo_concepto.nombre', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.conceptos.conceptos-component', compact('conceptos', 'tipos'));
    }
}
