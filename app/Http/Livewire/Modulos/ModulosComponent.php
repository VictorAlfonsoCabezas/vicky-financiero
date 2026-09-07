<?php

namespace App\Http\Livewire\Modulos;

use App\Models\Modulos;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModulosComponent extends Component
{
    public $id_seleccionado = 0;
    public $nombre = '';
    public $description = '';
    public $search = '';

    function modulos($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($id > 0) {
            $modulos = Modulos::find($id);
            $this->nombre  = $modulos->nombre;
            $this->description = $modulos->description;
        }
    }

    public function store()
    {
        $this->validate([
            "nombre" => "required",
            "description" => "required",
        ]);

        if ($this->id_seleccionado > 0) {
            $tipo = Modulos::find($this->id_seleccionado);
            $color = 'warning';
            $mensaje = 'Modulo editado correctamente';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
        } else {
            $tipo = new Modulos();
            $color = 'success';
            $mensaje = 'Modulo creado correctamente';
            $data = [
                'titulo' => 'Notificación',
                'color' => $color,
                'mensaje' => $mensaje
            ];
        }
        $tipo->company_id = Auth::user()->company_id;
        $tipo->nombre = $this->nombre;
        $tipo->description = $this->description;
        $tipo->status = true;
        $tipo->save();
        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function cambioEstado($id)
    {
        $modulos = Modulos::find($id);
        $modulos->status = !$modulos->status;
        $modulos->save();
    }

    public function limpiarFormulario()
    {
        $this->nombre = '';
        $this->description = '';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $modulos = Modulos::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->paginate(20);
        return view('livewire.modulos.modulos-component', compact('modulos'));
    }
}
