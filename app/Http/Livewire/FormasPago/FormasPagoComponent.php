<?php

namespace App\Http\Livewire\FormasPago;

use App\Models\FormasPago;
use App\Models\ClasificacionFormaPago;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FormasPagoComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $code = '';
    public $clasificacion_forma_pagos_id = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'code']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeFormas()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $formas = FormasPago::find($this->id_seleccionado);
        } else {
            $formas = new FormasPago();
        }
        $formas->nombre = $this->nombre;
        $formas->descripcion = $this->descripcion;
        $formas->clasificacion_forma_pagos_id = $this->clasificacion_forma_pagos_id;
        $formas->code = $this->code;
        $formas->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function editarFormas($id)
    {
        $this->id_seleccionado = $id;
        $formas = FormasPago::find($id);
        $this->nombre = $formas->nombre;
        $this->descripcion = $formas->descripcion;
    }

    public function borrarFormas($id)
    {
        FormasPago::find($id)->delete();
    }

    public function cambioFormas($id)
    {
        $formas = FormasPago::find($id);
        $formas->status = ($formas->status) ? false : true;
        $formas->save();
    }
    public function cambioDescargo($id)
    {
        $formas = FormasPago::find($id);
        $formas->credito_descargo_boveda = ($formas->credito_descargo_boveda) ? false : true;
        $formas->save();
    }

    protected $rules = [
        'nombre' => 'required',
        'descripcion' => 'required',
    ];

    public function render()
    {
        $formasPago = FormasPago::where('status', 1)->get();
        //dd($formasPago);
        $clasificacion = ClasificacionFormaPago::paginate(10);
        foreach ($clasificacion  as $val) {
            $val->formasPago = FormasPago::where('clasificacion_forma_pagos_id', $val->id)->get();
        }
        $grupos = ClasificacionFormaPago::where('status', 1)->get();
        return view('livewire.formas-pago.formas-pago-component', compact('formasPago', 'clasificacion', 'grupos'));
    }
}
