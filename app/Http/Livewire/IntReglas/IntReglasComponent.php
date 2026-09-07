<?php

namespace App\Http\Livewire\IntReglas;

use App\Models\IntReglas;
use App\Models\TipoAhorros;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IntReglasComponent extends Component
{
    public $id_seleccionado = 0;
    public $tipo_ahorro_id = 0;
    public $nombre = '';
    public $comentario = '';
    public $texto = '';
    public $operacion = '';
    public $valor = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['tipo_ahorro_id', 'nombre', 'comentario', 'texto', 'operacion', 'valor']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    protected $rules = [
        'tipo_ahorro_id' => 'required',
        'nombre' => 'required',
        'comentario' => 'required',
        'texto' => 'required',
        'operacion' => 'required',
        'valor' => 'required',
    ];

    public function store()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $regla = IntReglas::find($this->id_seleccionado);
        } else {
            $regla = new IntReglas();
        }
        $regla->company_id = Auth::user()->company_id;
        $regla->tipo_ahorro_id = ($this->tipo_ahorro_id == 0) ? null : $this->tipo_ahorro_id;
        $regla->nombre = $this->nombre;
        $regla->texto = $this->texto;
        $regla->operacion = $this->operacion;
        $regla->description = $this->comentario;
        $regla->valor = $this->valor;
        $regla->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarRegla($id)
    {
        IntReglas::find($id)->delete();
    }

    public function editarRegla($id)
    {
        $this->id_seleccionado = $id;
        $regla = IntReglas::find($id);
        $this->tipo_ahorro_id = (is_null($regla->tipo_ahorro_id)) ? 0 : $regla->tipo_ahorro_id;
        $this->nombre = $regla->nombre;
        $this->comentario = $regla->description;
        $this->texto = $regla->texto;
        $this->operacion = $regla->operacion;
        $this->valor = $regla->valor;
    }

    public function cambioEstado($id)
    {
        $regla = IntReglas::find($id);
        $regla->status = ($regla->status) ? false : true;
        $regla->save();
    }

    public function render()
    {
        $reglas = IntReglas::where('company_id', Auth::user()->company_id)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->paginate(10);
        $tipoAhorros = TipoAhorros::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        return view('livewire.int-reglas.int-reglas-component', compact('reglas', 'tipoAhorros'));
    }
}
