<?php

namespace App\Http\Livewire\TipoRetencion;

use App\Models\TipoRetencion;
use Livewire\Component;
use Livewire\WithPagination;

class TipoRetencionComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $name = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }

    private function limpiarFormulario()
    {
        $this->reset(['name']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioEstado($id)
    {
        $tiposRetencion = TipoRetencion::find($id);
        $tiposRetencion->status = !$tiposRetencion->status;
        $tiposRetencion->save();
    }

    public function storeImpuestos()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $impuestos = TipoRetencion::find($this->id_seleccionado);
        } else {
            $impuestos = new TipoRetencion();
        }
        $impuestos->name = strtoupper($this->name);
        $impuestos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'name' => 'required',
    ];

    public function editarImpuestos($id)
    {
        $this->id_seleccionado = $id;
        $tiposRetencion = TipoRetencion::find($id);
        $this->name = $tiposRetencion->name;
    }

    public function render()
    {
        $tiposRetencion = TipoRetencion::where('name', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.tipo-retencion.tipo-retencion-component', compact('tiposRetencion'));
    }
}
