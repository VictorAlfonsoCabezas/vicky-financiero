<?php

namespace App\Http\Livewire\TipoCuenta;

use App\Models\TipoCuenta;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TipoCuentaComponent extends Component
{
    use WithPagination;
    protected $paginateTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $apellido = '';

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
            $cuenta = TipoCuenta::find($this->id_seleccionado);
        } else {
            $cuenta = new TipoCuenta();
        }
        $cuenta->company_id = Auth::user()->company_id;
        $cuenta->nombre = $this->nombre;
        $cuenta->apellido = $this->apellido;
        $cuenta->save();
        $this->dispatchBrowserEvent('closeModal');
    }
    public function borrarCuent($id)
    {
        TipoCuenta::find($id)->delete();
    }

    public function editarCuent($id)
    {
        $this->id_seleccionado = $id;
        $cuenta = TipoCuenta::find($id);
        $this->nombre = $cuenta->nombre;
        $this->apellido = $cuenta->apellido;
    }

    public function cambioEstado($id)
    {
        $cuenta = TipoCuenta::find($id);
        $cuenta->status = ($cuenta->status) ? false : true;
        $cuenta->save();
    }

    public function cambioCuenta($id)
    {
        $cuenta = TipoCuenta::find($id);
        $cuenta->status = ($cuenta->status) ? false : true;
        $cuenta->save();
    }

    protected $rules = [
        'nombre' => 'required',
    ];

    public function render()
    {
        $cuenta = TipoCuenta::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.tipo-cuenta.tipo-cuenta-component', compact('cuenta'));
    }
}
