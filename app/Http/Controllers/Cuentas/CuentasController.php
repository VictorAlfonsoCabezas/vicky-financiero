<?php

namespace App\Http\Controllers\Cuentas;

use App\Http\Controllers\Controller;
use App\Models\Cajas;
use App\Models\CustomerTipoAhorros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuentasController extends Controller
{

    public function cuentas($id)
    {

        return view('cuentas.index')->with('id', $id);
    }

    public static function buscarNumeroCuenta()
    {
        $nuevoCodigo = '0000000001';
        $ultimoCodigo = CustomerTipoAhorros::where('company_id', Auth::user()->company_id)
            ->max('codigo');
            
        if ($ultimoCodigo) {
            $nuevoCodigo = str_pad((int) $ultimoCodigo + 1, 10, '0', STR_PAD_LEFT);
        }
        return $nuevoCodigo;
    }
}
