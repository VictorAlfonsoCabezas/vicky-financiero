<?php

namespace App\Http\Livewire\EstadoCivil;

use App\Models\EstadoCivil;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EstadoCivilComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeEstadoCivil()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $estado = EstadoCivil::find($this->id_seleccionado);
        } else {
            $estado = new EstadoCivil();
        }
        $estado->company_id = Auth::user()->company_id;
        $estado->nombre = $this->nombre;
        $estado->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function editarEstadoCivil($id)
    {
        $this->id_seleccionado = $id;
        $estado = EstadoCivil::find($id);
        $this->nombre = $estado->nombre;
    }

    public function borrarEstadoCivil($id)
    {
        EstadoCivil::find($id)->delete();
    }

    public function cambioEstado($id)
    {
        $estado = EstadoCivil::find($id);
        $estado->status = ($estado->status) ? false : true;
        $estado->save();
    }

    public function cambioDefecto($id)
    {
        EstadoCivil::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $estado = EstadoCivil::find($id);
        $estado->defecto = !$estado->defecto;
        $estado->save();
    }

    protected $rules = [
        'nombre' => 'required',
    ];

    public function render()
    {
        $estado = EstadoCivil::paginate(10);
        return view('livewire.estado-civil.estado-civil-component', compact('estado'));
    }
}
