<?php

namespace App\Http\Livewire\ListaRetencion;

use App\Models\ListaRetencion;
use App\Models\TipoRetencion;
use Livewire\Component;
use Livewire\WithPagination;

class ListaRetencionComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $id_seleccionado = 0;
    public $nombre = '';
    public $codigo = '';
    public $porcentaje = '';
    public $tipo_retencion_id = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'codigo', 'porcentaje', 'tipo_retencion_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioEstado($id)
    {
        $tiposRetencion = ListaRetencion::find($id);
        $tiposRetencion->status = !$tiposRetencion->status;
        $tiposRetencion->save();
    }

    public function storeImpuestos()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $impuestos = ListaRetencion::find($this->id_seleccionado);
        } else {
            $impuestos = new ListaRetencion();
        }
        $impuestos->nombre = strtoupper($this->nombre);
        $impuestos->codigo = $this->codigo;
        $impuestos->porcentaje = $this->porcentaje;
        $impuestos->tipo_retencion_id = $this->tipo_retencion_id;
        $impuestos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'nombre' => 'required',
        'codigo' => 'required',
        'porcentaje' => 'required',
        'tipo_retencion_id' => 'required',
    ];

    public function editarListaRetencion($id)
    {
        $this->id_seleccionado = $id;
        $tiposRetencion = ListaRetencion::find($id);
        $this->nombre = $tiposRetencion->nombre;
        $this->codigo = $tiposRetencion->codigo;
        $this->porcentaje = $tiposRetencion->porcentaje;
        $this->tipo_retencion_id = $tiposRetencion->tipo_retencion_id;
    }

    public function render()
    {
        $tiposRetencion = TipoRetencion::all();
        $listaRetencion = ListaRetencion::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('codigo', 'like', '%' . $this->search . '%')
            ->orWhere('porcentaje', 'like', '%' . $this->search . '%')
            ->paginate(20);
        return view('livewire.lista-retencion.lista-retencion-component', compact('listaRetencion', 'tiposRetencion'));
    }
}
