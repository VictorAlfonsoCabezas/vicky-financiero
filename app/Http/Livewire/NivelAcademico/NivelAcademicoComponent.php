<?php

namespace App\Http\Livewire\NivelAcademico;

use App\Models\NivelAcademico;
use Livewire\Component;
use Livewire\WithPagination;

class NivelAcademicoComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';

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

    public function store()
    {
        $this->validate();

        if ($this->id_seleccionado > 0) {
            $nivel = NivelAcademico::find($this->id_seleccionado);
        } else {
            $nivel = new NivelAcademico();
        }
        $nivel->nombre = strtoupper($this->nombre);
        $nivel->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrar($id)
    {
        NivelAcademico::find($id)->delete();
    }

    public function editar($id)
    {
        $this->id_seleccionado = $id;
        $nivel = NivelAcademico::find($id);
        $this->nombre = strtoupper($nivel->nombre);
    }

    public function cambioEstado($id)
    {
        $nivel = NivelAcademico::find($id);
        $nivel->status = !$nivel->status;
        $nivel->save();
    }

    public function cambioDefecto($id)
    {
        NivelAcademico::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $nivel = NivelAcademico::find($id);
        $nivel->defecto = !$nivel->defecto;
        $nivel->save();
    }

    protected $rules = [
        'nombre' => 'required',
    ];

    public function render()
    {
        $niveles = NivelAcademico::paginate(20);
        return view('livewire.nivel-academico.nivel-academico-component', compact('niveles'));
    }
}
