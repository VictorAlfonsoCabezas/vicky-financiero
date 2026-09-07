<?php

namespace App\Http\Livewire\Sedes;

use App\Models\Sedes;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SedesComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $id_seleccionado = 0;
    public $nombre = '';
    public $descripcion = '';
    public $establecimiento = '';
    public $matriz = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = $id;
        $this->limpiarFormulario();
        if ($this->id_seleccionado !== 0) {
            $sedes = Sedes::find($id);
            $this->nombre = $sedes->name;
            $this->descripcion = $sedes->descripcion;
            $this->establecimiento = $sedes->establecimiento;
            $this->matriz = $sedes->matriz;
        }
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'descripcion', 'establecimiento', 'matriz']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeConceptos()
    {
        $this->validate([
            'nombre' => 'required',
        ]);

        if ($this->id_seleccionado > 0) {
            $sedes = Sedes::find($this->id_seleccionado);
        } else {
            $sedes = new Sedes();
            $sedes->company_id = Auth::user()->company_id;
        }
        $sedes->name = $this->nombre;
        $sedes->descripcion = $this->descripcion;
        $sedes->establecimiento = $this->establecimiento;
        $sedes->matriz = $this->matriz;
        $sedes->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function cambioEstado($id)
    {
        $sedes = Sedes::find($id);
        $sedes->status = !$sedes->status;
        $sedes->save();
    }

    public function render()
    {
        $sedes = Sedes::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
        return view('livewire.sedes.sedes-component', compact('sedes'));
    }
}
