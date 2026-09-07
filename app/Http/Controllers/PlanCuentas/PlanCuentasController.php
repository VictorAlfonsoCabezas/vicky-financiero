<?php

namespace App\Http\Controllers\PlanCuentas;

use App\Http\Controllers\Controller;
use App\Models\PlanCuentas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanCuentasController extends Controller
{
    public function index()
    {
        return view('plan-cuentas/index');
    }

    public static function hijos($nivel1, $nivel2 = null, $nivel3 = null, $nivel4 = null, $nivel5 = null, $nivel6 = null, $nivel7 = null)
    {
        $nivelHijos = [];
        if (!is_null($nivel1) && $nivel2 == null && $nivel3 == null && $nivel4 == null && $nivel5 == null && $nivel6 == null && $nivel7 == null) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->whereNull('nivel2')
                ->whereNull('nivel3')
                ->whereNull('nivel4')
                ->whereNull('nivel5')
                ->whereNull('nivel6')
                ->whereNull('nivel7')
                ->get()
                ->toArray();
        } else if (!is_null($nivel1) && isset($nivel2) && $nivel3 == null && $nivel4 == null && $nivel5 == null && $nivel6 == null && $nivel7 == null) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->whereNotNull('nivel2')
                ->whereNull('nivel3')
                ->whereNull('nivel4')
                ->whereNull('nivel5')
                ->whereNull('nivel6')
                ->whereNull('nivel7')
                ->get()
                ->toArray();
        } else if (!is_null($nivel1) && isset($nivel2) && isset($nivel3) && $nivel4 == null && $nivel5 == null && $nivel6 == null && $nivel7 == null) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->where('nivel2', $nivel2)
                ->whereNotNull('nivel3')
                ->whereNull('nivel4')
                ->whereNull('nivel5')
                ->whereNull('nivel6')
                ->whereNull('nivel7')
                ->get()
                ->toArray();
        } else if (!is_null($nivel1) && isset($nivel2) && isset($nivel3) && isset($nivel4) && $nivel5 == null && $nivel6 == null && $nivel7 == null) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->where('nivel2', $nivel2)
                ->where('nivel3', $nivel3)
                ->whereNotNull('nivel4')
                ->whereNull('nivel5')
                ->whereNull('nivel6')
                ->whereNull('nivel7')
                ->get()
                ->toArray();
        } else if (!is_null($nivel1) && isset($nivel2) && isset($nivel3) && isset($nivel4) && isset($nivel5) && $nivel6 == null && $nivel7 == null) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->where('nivel2', $nivel2)
                ->where('nivel3', $nivel3)
                ->where('nivel4', $nivel4)
                ->whereNotNull('nivel5')
                ->whereNull('nivel6')
                ->whereNull('nivel7')
                ->get()
                ->toArray();
        } else if (!is_null($nivel1) && isset($nivel2) && isset($nivel3) && isset($nivel4) && isset($nivel5) && isset($nivel6) && $nivel7 == null) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->where('nivel2', $nivel2)
                ->where('nivel3', $nivel3)
                ->where('nivel4', $nivel4)
                ->where('nivel5', $nivel5)
                ->whereNotNull('nivel6')
                ->whereNull('nivel7')
                ->get()
                ->toArray();
        } else if (!is_null($nivel1) && isset($nivel2) && isset($nivel3) && isset($nivel4) && isset($nivel5) && isset($nivel6) && isset($nivel7)) {
            $nivelHijos = PlanCuentas::where('company_id', Auth::user()->company_id)
                ->where('nivel1', $nivel1)
                ->where('nivel2', $nivel2)
                ->where('nivel3', $nivel3)
                ->where('nivel4', $nivel4)
                ->where('nivel5', $nivel5)
                ->where('nivel6', $nivel6)
                ->whereNotNull('nivel7')
                ->get()
                ->toArray();
        }
        return $nivelHijos;
    }
}
