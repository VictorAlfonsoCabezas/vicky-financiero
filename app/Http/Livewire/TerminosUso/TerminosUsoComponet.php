<?php

namespace App\Http\Livewire\TerminosUso;

use App\Models\TerminosUso;
use App\Models\TerminosUsoClientes;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TerminosUsoComponet extends Component
{
    public $id_seleccionado = 0;
    public $name = '';
    public $description;

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }
    private function limpiarFormulario()
    {
        $this->reset(['name',  'description']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function storeTermino()
    {
        $this->validate(
            [

                'name' => 'required',
                'description' => 'required',


            ],
            [

                'name.required' => 'Necesita ingresar un nombre.',
                'description.required' => 'Necesita ingresar una descripción.',

            ]
        );
        if ($this->id_seleccionado > 0) {
            $formas = TerminosUso::find($this->id_seleccionado);
        } else {
            $formas = new TerminosUso();
            $formas->company_id = Auth::user()->company_id;
            $formas->date_create = date('Y-m-d');
            $formas->hour_create = date('H:i:s');
            $formas->user_id = Auth::user()->id;
            $formas->user_name = Auth::user()->username;
        }
        $formas->name = $this->name;
        $formas->description = $this->description;
        $formas->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function editarTermino($id)
    {
        $this->id_seleccionado = $id;
        $termino= TerminosUso::find($id);
        $this->name = $termino->name;
        $this->description = $termino->description;
    }

    public function borrarTermino($id)
    {
        $cont = TerminosUsoClientes::where('terminos_usos_id', $id)->count();
        if ($cont == 0) {
            TerminosUso::find($id)->delete();
        }
    }

    public function cambioEstado($id)
    {
        $termino = TerminosUso::find($id);
        $termino->status = ($termino->status) ? false : true;
        $termino->save();
    }


    public function render()
    {
        $terminos  = TerminosUso::paginate(10);
        return view('livewire.terminos-uso.terminos-uso-componet', compact('terminos'));
    }
}
