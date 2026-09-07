<?php

namespace App\Http\Livewire\Cajas;

use App\Models\Bovedas;
use App\Http\Controllers\Bovedas\BovedasController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CajasValorBovedaComponent extends Component
{
    public function render()
    {
        $sumaTotal = 0;
        $bovedas = Bovedas::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        foreach ($bovedas as $key => $value) {
            $value->saldoBoveda = BovedasController::saldoBovedas($value->id);
            $sumaTotal += $value->saldoBoveda;
        }
        $creditosVigentes = BovedasController::creditosVigentes();
        $valorTotal = $sumaTotal - $creditosVigentes;
        return view('livewire.cajas.cajas-valor-boveda-component', compact('valorTotal'));
    }
}
