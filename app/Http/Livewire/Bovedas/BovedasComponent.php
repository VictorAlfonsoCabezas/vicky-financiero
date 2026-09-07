<?php

namespace App\Http\Livewire\Bovedas;

use App\Models\Bovedas;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BovedasComponent extends Component
{
   

    protected $listeners = ['borrarBancos'];

    public $id_seleccionado = 0;
    public $fecha_creacion = '';
    public $nombre = '';
    public $descripcion = '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['fecha_creacion','nombre','descripcion']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
 
    public function storeBovedas()
    {
        $this->validate();
        if($this->id_seleccionado > 0){
            $bovedas = Bovedas::find($this->id_seleccionado);
        }else{
            $bovedas = new Bovedas();
        }
        $bovedas->company_id = Auth::user()->company_id;
        $bovedas->fecha_creacion = $this->fecha_creacion;
        $bovedas->nombre = $this->nombre;
        $bovedas->descripcion = $this->descripcion;
        $bovedas->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarBancosNotificar($id){
        $boveda = Bovedas::find($id);
        $data = [
            'id' => $id,
            'boveda' => $boveda,
        ];
        $this->dispatchBrowserEvent('notificarAccion', $data);
    }

    public function borrarBancos($id)
    {
        Bovedas::find($id)->delete();
    }

    public function editarBancos($id)
    {
        $this->id_seleccionado = $id;
        $bovedas = Bovedas::find($id);
        $this->fecha_creacion = $bovedas->fecha_creacion;
        $this->nombre = $bovedas->nombre;
        $this->descripcion = $bovedas->descripcion;  
    }
    
    protected $rules = [
        'nombre' => 'required',
        'descripcion' => 'required',
    ];

    public function cambioPrincipal($id)
    {
        $principal = Bovedas::find($id);
        $principal->principal = !$principal->principal;
        $principal->save();
    }

    public function cambioBoveda($id)
    {
        $boveda = Bovedas::find($id);
        $boveda->boveda = !$boveda->boveda;
        $boveda->save();
    }

    public function cambioEstado($id)
    {
        $bovedas = Bovedas::find($id);
        $bovedas->status = ($bovedas->status) ? false : true;
        $bovedas->save();
    }
   
    public function render()
    {
        $bovedas = Bovedas::paginate(10);
        return view('livewire.bovedas.bovedas-component',compact('bovedas'));
    }
}
