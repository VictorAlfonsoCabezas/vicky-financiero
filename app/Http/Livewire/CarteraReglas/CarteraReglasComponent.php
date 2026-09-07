<?php

namespace App\Http\Livewire\CarteraReglas;

use App\Models\CarteraReglas;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CarteraReglasComponent extends Component
{
    public $search = '';
    public $id_seleccionado = 0;
    public $nombre = '';
    public $mensaje = '';
    public $comparacion = '';
    public $dias_mora = '';

    function editarRegla($id)
    {
        $this->id_seleccionado = $id;
        $this->resetInput();
        if ($id > 0) {
            $tipo = CarteraReglas::find($id);
            $this->nombre = $tipo->nombre;
            $this->mensaje = $tipo->mensaje;
            $this->comparacion = $tipo->comparacion;
            $this->dias_mora = $tipo->dias_mora;
        }
    }

    public function store()
    {
        $this->validate([
            "nombre" => "required|min:3|max:50",
            "mensaje" => "required",
            "comparacion" => "required",
            "dias_mora" => "required|numeric",
        ]);

        if ($this->id_seleccionado > 0) {
            $regla = CarteraReglas::find($this->id_seleccionado);
        } else {
            $regla = new CarteraReglas();
            $regla->company_id = Auth::user()->company_id;
        }
        $regla->nombre = $this->nombre;
        $regla->mensaje = $this->mensaje;
        $regla->comparacion = $this->comparacion;
        $regla->dias_mora = $this->dias_mora;
        $regla->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function resetInput()
    {
        $this->nombre = '';
        $this->mensaje = '';
        $this->dias_mora = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioEstado($id)
    {
        $regla = CarteraReglas::find($id);
        $regla->status = ($regla->status) ? false : true;
        $regla->save();
    }

    public function borrarRegla($id)
    {
        $tipo = CarteraReglas::find($id)->delete();
    }

    public function render()
    {
        $carteraReglas = CarteraReglas::where('company_id', Auth::user()->company_id)->where('nombre', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.cartera-reglas.cartera-reglas-component', compact('carteraReglas'));
    }
}
