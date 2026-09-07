<?php

namespace App\Http\Livewire\Operacion;

use App\Models\Operacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OperacionComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $porcentaje = '100.00';
    public $search = '';


    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $operacion = Operacion::find($id);
            $this->nombre = $operacion->nombre;
            $this->descripcion = $operacion->descripcion;
            $this->porcentaje = $operacion->porcentaje;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'porcentaje']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeOperacion()
    {
        $this->validate([
            'nombre' => 'required',
            'porcentaje' => 'required'
        ]);
        if ($this->id_seleccionado > 0) {
            $operacion = Operacion::find($this->id_seleccionado);
        } else {
            $operacion = new Operacion();
            $operacion->company_id = Auth::user()->company_id;
        }
        $operacion->nombre = $this->nombre;
        $operacion->descripcion = $this->descripcion;
        $operacion->porcentaje = $this->porcentaje;
        $operacion->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarConcepto($id)
    {
        Operacion::find($id)->delete();
    }

    public function cambioEstado($id)
    {
        $operacion = Operacion::find($id);
        $operacion->status = !$operacion->status;
        $operacion->save();
    }

    public function render()
    {
        $operacion = Operacion::where('company_id', Auth::user()->company_id)
            ->where('status', true)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.operacion.operacion-component', compact('operacion'));
    }
}
