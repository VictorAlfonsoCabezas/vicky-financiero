<?php

namespace App\Http\Livewire\TipoConcepto;

use App\Models\TipoConcepto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TipoConceptoComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';
    public $search = '';


    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $concepto = TipoConcepto::find($id);
            $this->nombre = $concepto->nombre;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeConcepto()
    {
        $this->validate([
            'nombre' => 'required'
        ]);
        if ($this->id_seleccionado > 0) {
            $concepto = TipoConcepto::find($this->id_seleccionado);
        } else {
            $concepto = new TipoConcepto();
            $concepto->company_id = Auth::user()->company_id;
        }
        $concepto->nombre = $this->nombre;
        $concepto->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarConcepto($id)
    {
        TipoConcepto::find($id)->delete();
    }

    public function cambioEstado($id)
    {
        $concepto = TipoConcepto::find($id);
        $concepto->status = !$concepto->status;
        $concepto->save();
    }

    public function render()
    {
        $concepto = TipoConcepto::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.tipo-concepto.tipo-concepto-component', compact('concepto'));
    }
}
