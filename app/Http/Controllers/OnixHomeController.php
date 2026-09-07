<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Procesos\ProcesosController;
use Illuminate\Http\Request;
use App\User;
use App\Models\UsuarioRol;
use App\Models\Rol;
use App\Models\Company;
use App\Models\Customer;
use App\Models\FondoHeader;
use App\Models\Product;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Models\CustomerHistorial;
use Illuminate\Support\Facades\Auth;
use Response;

class OnixHomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $home = OnixHomeController::retonarHome();
        $company = Company::find(Auth::user()->company_id);
        return view($home)
            ->with('company', $company);
    }

    public static function retonarHome()
    {
        $customer = Customer::where('company_id', Auth::user()->company_id)->where('user_id', Auth::user()->id)->first();
        if ($customer) {
            return 'house';
        } else {
            return 'home';
        }
    }

    public function datosMeses()
    {
        $datos = array();
        $tipos = array();
        $ingresos = array();
        $retiros = array();
        foreach (config('constants.MESES') as $meses) {
            $anoActual = date('Y');
            $inicio = date('Y') . '-' . $meses['type'] . '-' . '01';
            $mesActual = date($meses['type']);
            $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mesActual, date('Y'));
            $fin = $anoActual . '-' . $meses['type'] . '-' . $cantidadDias;
            $historialIngreso = CustomerHistorial::where('type_transaction_action', 'S')
                ->where('company_id', Auth::user()->company_id)
                ->where('date_created', '>=', $inicio)
                ->where('date_created', '<=', $fin)
                ->where('status', true)
                ->sum('valor_movimiento');
            array_push($ingresos, $historialIngreso);
        }
        foreach (config('constants.MESES') as $meses) {
            $anoActual = date('Y');
            $inicio = $anoActual . '-' . $meses['type'] . '-' . '01';
            $mesActual = date($meses['type']);
            $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mesActual, date('Y'));
            $fin = $anoActual . '-' . $meses['type'] . '-' . $cantidadDias;
            $historialEgreso = CustomerHistorial::where('type_transaction_action', 'R')
                ->where('company_id', Auth::user()->company_id)
                ->where('date_created', '>=', $inicio)
                ->where('date_created', '<=', $fin)
                ->where('status', true)
                ->sum('valor_movimiento');
            array_push($retiros, $historialEgreso);
        }
        $datos = [
            'meses' => config('constants.SOLOMESES'),
            'ingresos' => $ingresos,
            'retiros' => $retiros,
        ];
        return Response::json($datos);
    }

    public function datosCreditos()
    {
        $datos = array();
        $tipos = array();
        $entregado = array();
        $intere = array();

        foreach (config('constants.MESES') as $meses) {
            $anoActual = date('Y');
            $inicio = date('Y') . '-' . $meses['type'] . '-' . '01';
            $mesActual = date($meses['type']);
            $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mesActual, date('Y'));
            $fin = $anoActual . '-' . $meses['type'] . '-' . $cantidadDias;
            $valoresEntregados = CreditFolderHeader::where('status', 'ENTREGADO')
                ->where('company_id', Auth::user()->company_id)
                ->where('date_verified', '>=', $inicio)
                ->where('date_verified', '<=', $fin)
                ->sum('valor_solicitado');
            array_push($entregado, $valoresEntregados);
        }


        foreach (config('constants.MESES') as $meses) {
            $anoActual = date('Y');
            $inicio = $anoActual . '-' . $meses['type'] . '-' . '01';
            $mesActual = date($meses['type']);
            $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $mesActual, date('Y'));
            $fin = $anoActual . '-' . $meses['type'] . '-' . $cantidadDias;
            $interesGenerado = CreditFolderDetail::where('status', 'PAGADA')
                ->where('company_id', Auth::user()->company_id)
                ->where('date_vencimiento', '>=', $inicio)
                ->where('date_vencimiento', '<=', $fin)
                ->sum('interes_periodo');
            array_push($intere, $interesGenerado);
        }

        $datos = [
            'meses' => config('constants.SOLOMESES'),
            'entregado' => $entregado,
            'interes' => $intere,
        ];
        return Response::json($datos);
    }

    public function paginaNoEncontrada(){
        abort(404);
    }
}
