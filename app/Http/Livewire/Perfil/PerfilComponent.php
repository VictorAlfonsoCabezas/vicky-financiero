<?php

namespace App\Http\Livewire\Perfil;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PerfilComponent extends Component
{
    public $nueva_clave = '';
    public $nueva_clave_confirmation = '';
    public function restablecerClave()
    {
        $this->validate([
            "nueva_clave" => "required|min:8",
            "nueva_clave_confirmation" => "required|min:8|same:nueva_clave",
        ]);
        $clave = $this->nueva_clave;
        $usuario = User::find(Auth::user()->id);
        $usuario->password = Hash::make($clave);
        $usuario->remember_token = bcrypt($clave);
        $usuario->token = $clave;
        $usuario->save();
        $color = 'success';
        $mensaje = 'Su constraseña se ha modificado correctamente';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('alerta', $data);
    }

    public function render()
    {
        $user = User::find(Auth::user()->id);
        return view('livewire.perfil.perfil-component', compact('user'));
    }
}
