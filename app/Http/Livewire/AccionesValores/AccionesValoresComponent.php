<?php

namespace App\Http\Livewire\AccionesValores;

use App\Models\AccionesValores;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AccionesValoresComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';
    public $operacion = '';
    public $descripcion = '';
    public $tipo = '';
    public $bloqueado = '';
    public $valor_defecto = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion']);
        $this->tipo = "SALDO";
        $this->bloqueado = "1";
        $this->operacion = "N";
        $this->valor_defecto = "0.00";
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeValor()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $valor = AccionesValores::find($this->id_seleccionado);
        } else {
            $valor = new AccionesValores();
        }
        $valor->company_id = Auth::user()->company_id;
        $valor->nombre = $this->nombre;
        $valor->fecha_creacion = date('Y-m-d H:i:s');
        $valor->descripcion = $this->descripcion;
        $valor->tipo = $this->tipo;
        $valor->bloqueado = $this->bloqueado;
        $valor->operacion = $this->operacion;
        $valor->valor_defecto = $this->valor_defecto;
        $valor->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'nombre' => 'required',
        'descripcion' => 'required',
        'tipo' => 'required',
        'bloqueado' => 'required',
        'operacion' => 'required',
    ];

    public function borrarValor($id)
    {
        AccionesValores::find($id)->delete();
    }

    public function editarValor($id)
    {
        $this->id_seleccionado = $id;
        $valor = AccionesValores::find($id);
        $this->nombre = $valor->nombre;
        $this->operacion = $valor->operacion;
        $this->descripcion = $valor->descripcion;
    }

    public function cambioEstado($id)
    {
        $valor = AccionesValores::find($id);
        $valor->status = ($valor->status) ? false : true;
        $valor->save();
    }


    public function render()
    {
        $acciones = AccionesValores::paginate(10);
        return view('livewire.acciones-valores.acciones-valores-component', compact('acciones'));
    }
}
