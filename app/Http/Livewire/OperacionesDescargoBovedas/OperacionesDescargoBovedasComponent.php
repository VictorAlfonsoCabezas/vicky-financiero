<?php

namespace App\Http\Livewire\OperacionesDescargoBovedas;

use App\Models\OperacionesDescargoBovedas;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OperacionesDescargoBovedasComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $nombre_corto = '';
    public $afecta = 'E';
    public $accion = 'S';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado > 0) {
            $this->id_seleccionado = $id;
            $operaciones = OperacionesDescargoBovedas::find($id);
            $this->nombre = $operaciones->nombre;
            $this->descripcion = $operaciones->descripcion;
            $this->nombre_corto = $operaciones->nombre_corto;
            $this->afecta = $operaciones->afecta;
            $this->accion = $operaciones->accion;
        }
        $this->dispatchBrowserEvent('openModal');
    }

    public function storeHeader()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $operaciones = OperacionesDescargoBovedas::find($this->id_seleccionado);
        } else {
            $operaciones = new OperacionesDescargoBovedas();
        }
        $operaciones->company_id = Auth::user()->company_id;
        $operaciones->nombre = $this->nombre;
        $operaciones->descripcion = $this->descripcion;
        $operaciones->nombre_corto = $this->nombre_corto;
        $operaciones->afecta = $this->afecta;
        $operaciones->accion = $this->accion;
        $operaciones->status = true;
        $operaciones->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'nombre' => 'required',
        'nombre_corto' => 'required',
        'afecta' => 'required',
        'accion' => 'required',
    ];

    public function borrarOperacion($id)
    {
        OperacionesDescargoBovedas::find($id)->delete();
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'nombre_corto', 'afecta', 'accion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioEstado($id)
    {
        $operaciones = OperacionesDescargoBovedas::find($id);
        $operaciones->status = !$operaciones->status;
        $operaciones->save();
    }

    public function render()
    {
        $operaciones = OperacionesDescargoBovedas::paginate(10);
        return view('livewire.operaciones-descargo-bovedas.operaciones-descargo-bovedas-component', compact('operaciones'));
    }
}
