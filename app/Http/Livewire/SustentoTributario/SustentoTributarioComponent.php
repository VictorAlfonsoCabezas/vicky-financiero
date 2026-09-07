<?php

namespace App\Http\Livewire\SustentoTributario;

use App\Models\SustentoTributario;
use Livewire\Component;
use Livewire\WithPagination;

class SustentoTributarioComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $id_seleccionado = 0;
    public $codigo_tributario = '';
    public $nombre = '';
    public $credito_tributario = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }

    private function limpiarFormulario()
    {
        $this->reset(['codigo_tributario', 'nombre', 'credito_tributario']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioEstado($id)
    {
        $tiposComprobante = SustentoTributario::find($id);
        $tiposComprobante->status = !$tiposComprobante->status;
        $tiposComprobante->save();
    }

    public function storeImpuestos()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $impuestos = SustentoTributario::find($this->id_seleccionado);
        } else {
            $impuestos = new SustentoTributario();
        }
        $impuestos->codigo_tributario = $this->codigo_tributario;
        $impuestos->nombre = strtoupper($this->nombre);
        $impuestos->credito_tributario = $this->credito_tributario;
        $impuestos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'codigo_tributario' => 'required',
        'nombre' => 'required',
    ];

    public function editarTipoComprobante($id)
    {
        $this->id_seleccionado = $id;
        $tiposRetencion = SustentoTributario::find($id);
        $this->codigo_tributario = $tiposRetencion->codigo_tributario;
        $this->nombre = $tiposRetencion->nombre;
        $this->credito_tributario = $tiposRetencion->credito_tributario;
    }
    public function render()
    {
        $sustentoTributario = SustentoTributario::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('codigo_tributario', 'like', '%' . $this->search . '%')
            ->paginate(20);
        return view('livewire.sustento-tributario.sustento-tributario-component', compact('sustentoTributario'));
    }
}
