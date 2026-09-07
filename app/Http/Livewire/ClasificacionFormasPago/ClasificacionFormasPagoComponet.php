<?php

namespace App\Http\Livewire\ClasificacionFormasPago;

use App\Models\ClasificacionFormaPago;
use App\Models\FormasPago;
use Livewire\Component;

class ClasificacionFormasPagoComponet extends Component
{
    public $id_seleccionado = 0;
    public $name = '';
    public $code = '';
    public $descripcion = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }
    private function limpiarFormulario()
    {
        $this->reset(['name', 'code', 'descripcion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioFormas($id)
    {
        $formas = ClasificacionFormaPago::find($id);
        $formas->status = ($formas->status) ? false : true;
        $formas->save();
    }

    public function editarFormas($id)
    {
        $this->id_seleccionado = $id;
        $formas = ClasificacionFormaPago::find($id);
        $this->name = $formas->name;
        $this->code = $formas->code;
        $this->descripcion = $formas->descripcion;
    }
    public function storeFormas()
    {
        $this->validate(
            [

                'name' => 'required',
                'code' => 'required',
                'descripcion' => 'required',

            ],
            [

                'name.required' => 'Necesita ingresar un nombre.',
                'code.required' => 'Necesita ingresar un código.',
                'descripcion.required' => 'Necesita ingresar una descripción.',

            ]
        );
        if ($this->id_seleccionado > 0) {
            $formas = ClasificacionFormaPago::find($this->id_seleccionado);
        } else {
            $formas = new ClasificacionFormaPago();
        }
        $formas->name = $this->name;
        $formas->code = $this->code;
        $formas->descripcion = $this->descripcion;
        $formas->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarClasificacion($id)
    {
        $cont = FormasPago::where('clasificacion_forma_pagos_id', $id)->count();
        if ($cont == 0) {
            ClasificacionFormaPago::find($id)->delete();
        }
    }

    public function render()
    {
        $clasificacion = ClasificacionFormaPago::paginate(10);
        return view('livewire.clasificacion-formas-pago.clasificacion-formas-pago-componet', compact('clasificacion'));
    }
}
