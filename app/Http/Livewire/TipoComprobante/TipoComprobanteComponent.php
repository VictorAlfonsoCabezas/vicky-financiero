<?php

namespace App\Http\Livewire\TipoComprobante;

use App\Models\TipoComprobante;
use Livewire\Component;
use Livewire\WithPagination;

class TipoComprobanteComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $id_seleccionado = 0;
    public $codigo = '';
    public $nombre = '';
    public $search = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
    }

    private function limpiarFormulario()
    {
        $this->reset(['codigo', 'nombre']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function cambioEstado($id)
    {
        $tiposComprobante = TipoComprobante::find($id);
        $tiposComprobante->status = !$tiposComprobante->status;
        $tiposComprobante->save();
    }

    public function storeImpuestos()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $impuestos = TipoComprobante::find($this->id_seleccionado);
        } else {
            $impuestos = new TipoComprobante();
        }
        $impuestos->codigo = $this->codigo;
        $impuestos->nombre = strtoupper($this->nombre);
        $impuestos->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    protected $rules = [
        'nombre' => 'required',
        'codigo' => 'required',
    ];

    public function editarTipoComprobante($id)
    {
        $this->id_seleccionado = $id;
        $tiposRetencion = TipoComprobante::find($id);
        $this->nombre = $tiposRetencion->nombre;
        $this->codigo = $tiposRetencion->codigo;
    }

    public function render()
    {
        $tiposComprobante = TipoComprobante::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('codigo', 'like', '%' . $this->search . '%')
            ->paginate(20);
        return view('livewire.tipo-comprobante.tipo-comprobante-component', compact('tiposComprobante'));
    }
}
