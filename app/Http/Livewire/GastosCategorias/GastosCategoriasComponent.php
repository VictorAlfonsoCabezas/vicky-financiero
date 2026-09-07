<?php

namespace App\Http\Livewire\GastosCategorias;

use App\Models\GastosCategorias;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GastosCategoriasComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $search = '';

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

    public function storeCategorias()
    {
        $this->validate();
        if($this->id_seleccionado > 0){
            $cat = GastosCategorias::find($this->id_seleccionado);
        }else{
            $cat= new GastosCategorias();
        }
        $cat->nombre = strtoupper($this->nombre);
        $cat->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function editarCategorias($id)
    {
        $this->id_seleccionado = $id;
        $cat = GastosCategorias::find($id);
        $this->nombre = strtoupper($cat->nombre);
    }

    public function borrarCategorias($id)
    {
        GastosCategorias::find($id)->delete();
    }

    public function cambioEstado($id)
    {
        $cat = GastosCategorias::find($id);
        $cat->status = ($cat->status) ? false : true;
        $cat->save();
    }

    protected $rules = [
        'nombre' => 'required',
    ];

    public function render()
    {
        $categorias = GastosCategorias::where('nombre', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.gastos-categorias.gastos-categorias-component',compact('categorias'));
    }
}
