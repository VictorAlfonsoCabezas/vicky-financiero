<?php

namespace App\Http\Livewire\TipoDocumento;

use App\Models\TipoDocumento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TipoDocumentoComponent extends Component
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
            $documento = TipoDocumento::find($this->id_seleccionado);
        } else {
            $documento = new TipoDocumento();
        }
        $documento->company_id = Auth::user()->company_id;
        $documento->nombre = $this->nombre;
        $documento->save();
        $this->dispatchBrowserEvent('closeModal');
    }
    
    public function borrarDocumento($id)
    {
        TipoDocumento::find($id)->delete();
    }

    public function editarDocumento($id)
    {
        $this->id_seleccionado = $id;
        $documento = TipoDocumento::find($id);
        $this->nombre = $documento->nombre;
    }

    public function cambioEstado($id)
    {
        $documento = TipoDocumento::find($id);
        $documento->status = ($documento->status) ? false : true;
        $documento->save();
    }

    public function cambioDefecto($id)
    {
        TipoDocumento::where('defecto', true)->where('id', '!=', $id)->update(['defecto' => false]);
        $documento = TipoDocumento::find($id);
        $documento->defecto = !$documento->defecto;
        $documento->save();
    }

    public function cambioCuenta($id)
    {
        $documento = TipoDocumento::find($id);
        $documento->status = ($documento->status) ? false : true;
        $documento->save();
    }

    protected $rules = [
        'nombre' => 'required',
    ];

    public function render()
    {
        $documento = TipoDocumento::where('company_id', Auth::user()->company_id)->paginate(10);
        return view('livewire.tipo-documento.tipo-documento-component', compact('documento'));
    }
}
