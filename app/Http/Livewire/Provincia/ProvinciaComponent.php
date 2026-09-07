<?php

namespace App\Http\Livewire\Provincia;

use App\Models\Country;
use App\Models\Provincia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProvinciaComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $country_id = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'country_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeProvincia()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $provincia = Provincia::find($this->id_seleccionado);
        } else {
            $provincia = new Provincia();
        }
        $provincia->nombre = $this->nombre;
        $provincia->country_id = $this->country_id;
        $provincia->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'nombre' => 'required',
        'country_id' => 'required',
    ];

    public function borrarProvincia($id)
    {
        Provincia::find($id)->delete();
    }

    public function editarProvincia($id)
    {
        $this->id_seleccionado = $id;
        $provincia = Provincia::find($id);
        $this->nombre = $provincia->nombre;
        $this->country_id = $provincia->country_id;
    }

    public function cambioEstado($id)
    {
        $provincia = Provincia::find($id);
        $provincia->status = ($provincia->status) ? false : true;
        $provincia->save();
    }

    public function cambioDefecto($id)
    {
        Provincia::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $provincia = Provincia::find($id);
        $provincia->defecto = !$provincia->defecto;
        $provincia->save();
    }

    public function render()
    {
        $country = Country::get();
        $provincia = Provincia::where('nombre', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.provincia.provincia-component', compact('provincia', 'country'));
    }
}
