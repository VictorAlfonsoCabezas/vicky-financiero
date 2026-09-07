<?php

namespace App\Http\Livewire\House;

use App\Models\Customer;
use App\Models\TerminosUso;
use App\Models\TerminosUsoClientes;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class HouseComponent extends Component
{
    public $nueva_clave = '';
    public $nueva_clave_confirmation = '';
    public $terminoId = '';

    public function abrirModal()
    {
        $this->limpiarFormulario();
    }

    private function limpiarFormulario()
    {
        $this->reset(['nueva_clave', 'nueva_clave_confirmation']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

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

    public function aceptarTerminosUso()
    {
        $customer = Customer::where('user_id', Auth::user()->id)->first();
        $data = [
            'company_id' => $customer->company_id,
            'user_id' => $customer->user_id,
            'customer_id' => $customer->id,
            'terminos_usos_id' => $this->terminoId,
            'periodo' => date('Y'),
            'date_create' => date('Y-m-d'),
            'hour_create' => date('Y-m-d'),

        ];
        TerminosUsoClientes::create($data);
        $color = 'success';
        $mensaje = 'Acepto los terminos y politicas de uso';
        $data = [
            'titulo' => 'Notificación',
            'color' => $color,
            'mensaje' => $mensaje
        ];
        $this->dispatchBrowserEvent('alerta', $data);
        $customer = Customer::where('user_id', Auth::user()->id)->first();
        $periodo = date('Y');
        $terminos = TerminosUso::where('status', true)->get();
        $terminoAceptar = array();
        $existeAceptar = false;
        $entro = false;
        foreach ($terminos as $ter) {
            $acepto = TerminosUsoClientes::where('user_id', Auth::user()->id)
                ->where('customer_id', $customer->id)
                ->where('terminos_usos_id', $ter->id)
                ->where('periodo', $periodo)
                ->first();
            if ($acepto == null && $entro == false) {
                $existeAceptar = true;
                $terminoAceptar = $ter;
                $this->terminoId = $ter->id;
                $entro = true;
            }
        }
        if($entro == false ){
            $this->dispatchBrowserEvent('closeModal');
        }
        $this->render();
    }

    public function render()
    {
        $customer = Customer::where('user_id', Auth::user()->id)->first();
        $periodo = date('Y');
        $terminos = TerminosUso::where('status', true)->get();
        $terminoAceptar = array();
        $existeAceptar = false;
        $entro = false;
        foreach ($terminos as $ter) {
            $acepto = TerminosUsoClientes::where('user_id', Auth::user()->id)
                ->where('customer_id', $customer->id)
                ->where('terminos_usos_id', $ter->id)
                //->where('periodo', $periodo)
                ->first();
            if ($acepto == null && $entro == false) {
                $existeAceptar = true;
                $terminoAceptar = $ter;
                $this->terminoId = $ter->id;
                $entro = true;
            }
        }

        return view('livewire.house.house-component', compact('customer', 'existeAceptar', 'terminoAceptar'));
    }
}
