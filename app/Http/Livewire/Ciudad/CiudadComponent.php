<?php

namespace App\Http\Livewire\Ciudad;

use App\Models\Ciudad;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;

class CiudadComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $parroquia_id = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'parroquia_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeCiudad()
    {
        $this->validate();

        if ($this->id_seleccionado > 0) {
            $ciudad = Ciudad::find($this->id_seleccionado);
        } else {
            $ciudad = new Ciudad();
        }
        $ciudad->nombre = $this->nombre;
        $ciudad->parroquia_id = $this->parroquia_id;
        $ciudad->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarCiudad($id)
    {
        Ciudad::find($id)->delete();
    }

    public function editarCiudad($id)
    {
        $this->id_seleccionado = $id;
        $ciudad = Ciudad::find($id);
        $this->nombre = $ciudad->nombre;
        $this->parroquia_id = $ciudad->parroquia_id;
    }

    public function cambioEstado($id)
    {
        $ciudad = Ciudad::find($id);
        $ciudad->status = ($ciudad->status) ? false : true;
        $ciudad->save();
    }

    public function cambioDefecto($id)
    {
        Ciudad::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $ciudad = Ciudad::find($id);
        $ciudad->defecto = !$ciudad->defecto;
        $ciudad->save();
    }

    protected $rules = [
        'nombre' => 'required',
        'parroquia_id' => 'required',
    ];

    public function render()
    {
        $country = Country::where('status', true)->get();
        $ciudad = Ciudad::where('nombre', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.ciudad.ciudad-component', compact('ciudad', 'country'));
    }
}
