<?php

namespace App\Http\Livewire\Meses;

use App\Models\Meses;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MesesComponent extends Component
{
    public $id_seleccionado = 0;
    public $codigo = '';
    public $mes = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
    }

    public function storeMeses()
    {
        if ($this->id_seleccionado > 0) {
            $month = Meses::find($this->id_seleccionado);
        } else {
            $month = new Meses();
        }
        $month->codigo = $this->codigo;
        $month->mes = $this->mes;
        $month->save();
        // cierra el modal
        $this->dispatchBrowserEvent('closeModal');
    }
   

    public function borrarMeses($id)
    {
        Meses::find($id)->delete();
    }

    public function editarMeses($id)
    {
        $this->id_seleccionado = $id;
        $month = Meses::find($id);
        $this->codigo = $month->codigo;
        $this->mes = $month->mes;
    }

    public function cambioEstado($id)
    {
        $month = Meses::find($id);
        $month->status = ($month->status) ? false : true;
        $month->save();
    }

    public function render()
    {
        $meses = Meses::where('company_id', Auth::user()->company_id)->paginate(12);
        return view('livewire.meses.meses-component', compact('meses'));
    }
}
