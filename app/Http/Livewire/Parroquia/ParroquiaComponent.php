<?php

namespace App\Http\Livewire\Parroquia;

use App\Models\Parroquia;
use App\Models\Provincia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ParroquiaComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $provincia_id = '';
    public $search = '';


    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'provincia_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeParroquia()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $parroquia = Parroquia::find($this->id_seleccionado);
        } else {
            $parroquia = new Parroquia();
        }
        $parroquia->nombre = $this->nombre;
        $parroquia->provincia_id = $this->provincia_id;
        $parroquia->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarParroquia($id)
    {
        Parroquia::find($id)->delete();
    }

    public function editarParroquia($id)
    {
        $this->id_seleccionado = $id;
        $parroquia = Parroquia::find($id);
        $this->nombre = $parroquia->nombre;
        $this->provincia_id = $parroquia->provincia_id;
    }

    public function cambioEstado($id)
    {
        $parroquia = Parroquia::find($id);
        $parroquia->status = ($parroquia->status) ? false : true;
        $parroquia->save();
    }

    public function cambioDefecto($id)
    {
        Parroquia::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $parroquia = Parroquia::find($id);
        $parroquia->defecto = !$parroquia->defecto;
        $parroquia->save();
    }

    protected $rules = [
        'nombre' => 'required',
        'provincia_id' => 'required',
    ];

    public function render()
    {
        $provincia = Provincia::get();
        $parroquia = Parroquia::where('nombre', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.parroquia.parroquia-component', compact('provincia', 'parroquia'));
    }
}
