<?php

namespace App\Http\Livewire\Country;

use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;

class CountryComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $codigo_pais = '';
    public $codigo_llamada = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'codigo_pais', 'codigo_llamada']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $country = Country::find($this->id_seleccionado);
        } else {
            $country = new Country();
        }
        $country->nombre = $this->nombre;
        $country->codigo_pais = $this->codigo_pais;
        $country->codigo_llamada = $this->codigo_llamada;
        $country->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'nombre' => 'required',
        'codigo_pais' => 'required',
        'codigo_llamada' => 'required',
    ];

    public function borrarPais($id)
    {
        Country::find($id)->delete();
    }

    public function editarPais($id)
    {
        $this->id_seleccionado = $id;
        $pais = Country::find($id);
        $this->nombre = $pais->nombre;
        $this->codigo_pais = $pais->codigo_pais;
        $this->codigo_llamada = $pais->codigo_llamada;
    }

    public function cambioEstado($id)
    {
        $pais = Country::find($id);
        $pais->status = ($pais->status) ? false : true;
        $pais->save();
    }

    public function cambioDefecto($id)
    {
        Country::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $pais = Country::find($id);
        $pais->defecto = !$pais->defecto;
        $pais->save();
    }

    public function render()
    {
        $country = Country::paginate(10);
        return view('livewire.country.country-component', compact('country'));
    }
}
