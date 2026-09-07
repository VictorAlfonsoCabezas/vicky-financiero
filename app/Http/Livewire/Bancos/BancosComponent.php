<?php

namespace App\Http\Livewire\Bancos;

use App\Models\Bancos;
use App\Models\TipoCuenta;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BancosComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $numero_cuenta = '';
    public $tipo_cuenta_id = '';
    public $tipoCuentas = [];

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'numero_cuenta', 'tipo_cuenta_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeBancos()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $bancos = Bancos::find($this->id_seleccionado);
        } else {
            $bancos = new Bancos();
            $bancos->fecha_creacion = date('Y-m-d H:i:s');
        }
        $bancos->company_id = Auth::user()->company_id;
        $bancos->tipo_cuenta_id = $this->tipo_cuenta_id;
        $bancos->nombre = $this->nombre;
        $bancos->descripcion = $this->descripcion;
        $bancos->numero_cuenta = $this->numero_cuenta;
        $bancos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarBancos($id)
    {
        bancos::find($id)->delete();
    }

    public function editarBancos($id)
    {
        $this->id_seleccionado = $id;
        $bancos = Bancos::find($id);
        $this->tipo_cuenta_id = $bancos->tipo_cuenta_id;
        $this->nombre = $bancos->nombre;
        $this->descripcion = $bancos->descripcion;
        $this->numero_cuenta = $bancos->numero_cuenta;
    }

    protected $rules = [
        'nombre' => 'required'
    ];


    public function cambioEstado($id)
    {
        $bancos = Bancos::find($id);
        $bancos->status = ($bancos->status) ? false : true;
        $bancos->save();
    }

    public function mount()
    {
        $tipoCuenta = TipoCuenta::where('company_id', Auth::user()->company_id)->get()->toArray();
        $this->tipoCuentas = $tipoCuenta;
    }

    public function render()
    {
        $bancos = Bancos::paginate(10);
        return view('livewire.bancos.bancos-component', compact('bancos'));
    }
}
