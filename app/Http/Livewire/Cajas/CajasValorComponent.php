<?php

namespace App\Http\Livewire\Cajas;

use App\Http\Controllers\Base\BaseController;
use App\Models\Cajas;
use App\Models\CustomerHistorial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CajasValorComponent extends Component {

    public function render() {
        $valor = 0;
        $valores = 0;
        $cajaAbierta = Cajas::where('status', 'ABIERTA')->where('date_inicial', date('Y-m-d'))->where('user_inicial_id', Auth::user()->id)->first();
        if ($cajaAbierta) {
            $valores = BaseController::valoresCajasBoveda($cajaAbierta->id);
            $fecha = date('Y-m-d');
            $valor = BaseController::valorTotalMovimientosUsuario($fecha);
        }else{
            $valores = 0;
        }
        $valorTotal = $valores + $valor;
        return view('livewire.cajas.cajas-valor-component', compact('valorTotal'));
    }

}
