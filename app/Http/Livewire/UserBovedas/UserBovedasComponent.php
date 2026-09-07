<?php

namespace App\Http\Livewire\UserBovedas;

use App\Models\Bovedas;
use App\Models\UserBovedas;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserBovedasComponent extends Component
{
    public $id_seleccionado = 0;
    public $user_id = '';
    public $bovedas_id= '';

    public function abrirModal($id)
    {
        $this->id_seleccionado = 0;
        $this->limpiarFormulario();
        $this->dispatchBrowserEvent('openModal');
    }

    private function limpiarFormulario()
    {
        $this->reset(['user_id', 'bovedas_id']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function storeUserBovedas()
    {
        $this->validate();
        if ($this->id_seleccionado > 0) {
            $bovedas = UserBovedas::find($this->id_seleccionado);
        } else {
            $bovedas= new UserBovedas();
        }
        $bovedas->user_id = $this->user_id;
        $bovedas->bovedas_id = $this->bovedas_id;
        $bovedas->save();
        $this->dispatchBrowserEvent('closeModal');
    }

    public function borrarBovedas($id)
    {
        UserBovedas::find($id)->delete();
    }

    public function editarBovedas($id)
    {
        $this->id_seleccionado = $id;
        $bovedas = UserBovedas::find($id);
        $this->user_id = $bovedas->user_id;
        $this->bovedas_id = $bovedas->bovedas_id;
    }

    protected $rules = [
        'user_id' => 'required',
        'bovedas_id' => 'required',
    ];


    
    public function render()
    {
        $bovedas = Bovedas::where('company_id', Auth::user()->company_id)->get();
        $usuarios = User::where('company_id', Auth::user()->company_id)->get();
        $userbovedas = UserBovedas::paginate(10);
        return view('livewire.user-bovedas.user-bovedas-component',compact('userbovedas', 'bovedas', 'usuarios'));
    }
}
