<?php

namespace App\Http\Livewire\DocumentosParametrizables;

use App\Models\DocumentosParametrizables;
use App\Models\VariablesDocumentos;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DocumentosFormatoComponent extends Component
{
    public $formato; // Guardar el ID
    public $documento;
    public $nombredocumento;
    public $valorInsertar = '';
    public $nombreVariable = '';
    public $contenido = '';
    protected $listeners = ['updateContenido'];

    public function updateContenido($valor)
    {
        $this->contenido = $valor;
    }

    public function valorInsertadoCambiado()
    {
        $variable = VariablesDocumentos::find($this->valorInsertar);
        $this->nombreVariable = $variable ? '*' . $variable->variable . '*' : '';
    }
    public function actualizarContenido()
    {
        // Emite el contenido actual del editor Froala
        $this->emit('updateContenido', $this->contenido);
    }

    public function resetEditorContent()
    {
        $this->emit('resetEditor'); // Emitir evento para reiniciar el editor en JavaScript

    }

    public function guardarFormato()
    {
        $this->validate(['nombredocumento' => 'required|string|max:255', 'contenido' => 'required|string']);
        $existeFormato = $this->formato
            ? DocumentosParametrizables::where('company_id', Auth::user()->company_id)->findOrFail($this->formato)
            : DocumentosParametrizables::where('company_id', Auth::user()->company_id)->where('formato', $this->nombredocumento)->first();
        if ($existeFormato) {
            $formato = DocumentosParametrizables::find($existeFormato->id);
        } else {
            $formato = new DocumentosParametrizables();
            $formato->company_id = Auth::user()->company_id;
            $formato->formato = $this->nombredocumento;
            $formato->date_created = date('Y-m-d');
            $formato->hour_created = date('H:i:s');
            $formato->user_created_id = Auth::user()->id;
            $formato->user_created_name = Auth::user()->username;
        }
        $formato->content = $this->contenido;
        $formato->formato = $this->nombredocumento;
        $formato->save();
        $this->formato = $formato->id;
        $this->dispatchBrowserEvent('alerta', ['titulo' => 'Documento guardado', 'color' => 'success', 'mensaje' => 'El formato se guardó correctamente.']);
    }

    public function mount($formato, $documento)
    {
        $this->formato = $formato;
        $this->documento = $documento;
        if ($formato) {
            $saved = DocumentosParametrizables::where('company_id', Auth::user()->company_id)->findOrFail($formato);
            $this->nombredocumento = $saved->formato;
            $this->contenido = $saved->content;
            return;
        }
        switch ($documento) {
            case 1:
                $existeFormato = DocumentosParametrizables::where('company_id', Auth::user()->company_id)
                    ->where('formato', 'PAGARE')->first();
                if ($existeFormato) {
                    $this->formato = $existeFormato->id;
                    $this->contenido = $existeFormato->content;
                } else {
                    $this->contenido = 'PAGARE';
                }

                $this->nombredocumento = 'PAGARE';
                break;
            default:
                $this->nombredocumento = '';
                break;
        }
    }
    public function render()
    {
        $variables =  VariablesDocumentos::all();
        return view('livewire.documentos-parametrizables.documentos-formato-component', compact('variables'));
    }
}
