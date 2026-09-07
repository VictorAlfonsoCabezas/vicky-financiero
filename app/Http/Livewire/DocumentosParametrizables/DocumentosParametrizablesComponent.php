<?php

namespace App\Http\Livewire\DocumentosParametrizables;

use App\Models\DocumentosParametrizables;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DocumentosParametrizablesComponent extends Component
{
    public $contenido = ''; // Almacena el HTML generado
    public $tipo_formato = ''; 

    public function updatedContenido($value)
    {
        // Se ejecuta cada vez que se actualiza el contenido del editor
        $this->contenido = $value;
    }
    public function abrirModal($id)
    {
        $this->resetEditorContent();
    }

    public function resetEditorContent()
    {
        $this->emit('resetEditor'); // Emitir evento para reiniciar el editor en JavaScript

    }

    public function guardarFormato()
    {
        $this->validate(
            [
                'contenido' => 'required',
                'tipo_formato' => 'required|in:1',

            ],
            [
                'contenido.required' => 'El campo establecimiento es obligatorio.',

            ]
        );
        $editor = new DocumentosFormatoComponent();
        $editor->nombredocumento = 'PAGARE';
        $editor->contenido = $this->contenido;
        $editor->guardarFormato();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function generarDocumento(){
        $this->validate(
            [
                'tipo_formato' => 'required|in:1',
               
            ],
            [
                'tipo_formato.required' => 'Debe Seleecionar un formato.',
                
            ]
        );

        return redirect()->to('/formatoCreado/0/'.$this->tipo_formato); 
    }

    public function render()
    {
        $formatos = DocumentosParametrizables::where('company_id', Auth::user()->company_id)->get();
        return view('livewire.documentos-parametrizables.documentos-parametrizables-component', compact('formatos'));
    }
}
