<?php

namespace App\Http\Livewire\Impuestos;

use App\Models\Impuestos;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ImpuestosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $valor;
    public $codigo;
    public $por_defecto = false;
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'valor', 'codigo', 'por_defecto']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeImpuestos()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $impuestos = Impuestos::find($this->id_seleccionado);
        } else {
            $impuestos = new Impuestos();
        }
        $impuestos->company_id = Auth::user()->company_id;
        $impuestos->nombre = $this->nombre;
        $impuestos->descripcion = $this->descripcion;
        $impuestos->valor = $this->valor;
        $impuestos->codigo = $this->codigo;
        $impuestos->por_defecto = $this->por_defecto;
        $impuestos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function editarImpuestos($id)
    {
        $this->id_seleccionado = $id;
        $impuestos = Impuestos::find($id);
        $this->nombre = $impuestos->nombre;
        $this->descripcion = $impuestos->descripcion;
        $this->valor = $impuestos->valor;
        $this->codigo = $impuestos->codigo;
        $this->por_defecto = $impuestos->por_defecto;
    }

    public function borrarImpuestos($id)
    {
        Impuestos::find($id)->delete();
    }

    public function cambioEstado($id)
    {
        $impuestos = Impuestos::find($id);
        $impuestos->status = !$impuestos->status;
        $impuestos->save();
    }

    protected $rules = [
        'nombre' => 'required',
        'descripcion' => 'required',
        'valor' => 'required',
        'codigo' => 'required',
    ];

    public function render()
    {
        $impuestos = Impuestos::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('valor', 'like', '%' . $this->search . '%')
            ->paginate(10);
        return view('livewire.impuestos.impuestos-component', compact('impuestos'));
    }
}
