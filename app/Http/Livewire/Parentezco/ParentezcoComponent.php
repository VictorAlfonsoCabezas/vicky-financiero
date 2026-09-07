<?php

namespace App\Http\Livewire\Parentezco;

use App\Models\Parentezco;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ParentezcoComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $description = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre']);
        $this->reset(['description']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeParentezco()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $parentezco = Parentezco::find($this->id_seleccionado);
        } else {
            $parentezco = new Parentezco();
        }
        $parentezco->nombre = $this->nombre;
        $parentezco->description = $this->description;
        $parentezco->save();
        // cierra el modal
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarParentezco($id)
    {
        Parentezco::find($id)->delete();
    }

    public function editarParentezco($id)
    {
        $this->id_seleccionado = $id;
        $parentezco = Parentezco::find($id);
        $this->nombre = $parentezco->nombre;
        $this->description = $parentezco->description;
    }

    public function cambioEstado($id)
    {
        $parentezco = Parentezco::find($id);
        $parentezco->status = ($parentezco->status) ? false : true;
        $parentezco->save();
    }

    public function cambioDefecto($id)
    {
        $parentezco = Parentezco::find($id);
        $parentezco->defecto = !$parentezco->defecto;
        $parentezco->save();
    }

    protected $rules = [
        'nombre' => 'required',
        'description' => 'required',
    ];

    public function render()
    {
        $parentezco = Parentezco::paginate(10);
        return view('livewire.parentezco.parentezco-component', compact('parentezco'));
    }
}
