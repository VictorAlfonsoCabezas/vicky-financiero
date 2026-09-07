<?php

namespace App\Http\Livewire\Genero;

use App\Models\Genero;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GeneroComponent extends Component
{
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
            $genero = $this->companyQuery()->findOrFail($this->id_seleccionado);
        } else {
            $genero = new Genero();
        }
        $genero->company_id = Auth::user()->company_id;
        $genero->nombre = $this->nombre;
        $genero->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarGenero($id)
    {
        $this->companyQuery()->findOrFail($id)->delete();
    }

    public function editarGenero($id)
    {
        $this->id_seleccionado = $id;
        $genero = $this->companyQuery()->findOrFail($id);
        $this->nombre = $genero->nombre;
    }

    protected $rules = [
        'nombre' => 'required|string|max:30',
    ];

    public function cambioGenero($id)
    {
        $genero = $this->companyQuery()->findOrFail($id);
        $genero->status = ($genero->status) ? false : true;
        $genero->save();
    }

    public function cambioEstado($id)
    {
        $genero = $this->companyQuery()->findOrFail($id);
        $genero->status = ($genero->status) ? false : true;
        $genero->save();
    }

    public function cambioDefecto($id)
    {
        DB::transaction(function () use ($id) {
            $genero = $this->companyQuery()->findOrFail($id);
            $this->companyQuery()->where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
            $genero->defecto = !$genero->defecto;
            $genero->save();
        });
    }

    private function companyQuery()
    {
        return Genero::where('company_id', Auth::user()->company_id);
    }

    public function render()
    {
        $genero = Genero::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.genero.genero-component', compact('genero'));
    }
}
