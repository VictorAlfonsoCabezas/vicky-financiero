<?php

namespace App\Http\Controllers\AccionesDetalle;

use App\Http\Controllers\Controller;
use App\Models\AccionesDetalle;
use App\Models\AccionesDetalleValores;
use App\Models\AccionesValores;
use App\Models\Company;
use App\Models\Customer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class AccionesDetalleController extends Controller
{
    public function detalle($id)
    {
        return view('acciones-detalle/index')->with('id', $id);
    }

    public static function buscarValor($valores, $detalle)
    {
        $valor = 0;
        $accionesDetalleValores = AccionesDetalleValores::where('company_id', Auth::user()->company_id)
            ->where('acciones_valores_id', $valores)
            ->where('acciones_detalle_id', $detalle)
            ->first();
        if($accionesDetalleValores){
           $valor = $accionesDetalleValores->valor;
        }
        return $valor;
    }

    public static function verBloqueado($valores)
    {
        $accionesValores = AccionesValores::find($valores);
        return $accionesValores->bloqueado;
    }

    public static function calcularTotal($detalle)
    {
        $resultado = AccionesDetalleValores::select(DB::raw('SUM(CASE WHEN acciones_valores.operacion = "S" THEN acciones_detalle_valores.valor ELSE 0 END) - SUM(CASE WHEN acciones_valores.operacion = "R" THEN acciones_detalle_valores.valor ELSE 0 END) AS resultado'))
            ->join('acciones_valores', 'acciones_detalle_valores.acciones_valores_id', '=', 'acciones_valores.id')
            ->join('acciones_detalle', 'acciones_detalle_valores.acciones_detalle_id', '=', 'acciones_detalle.id')
            ->where('acciones_detalle.company_id', Auth::user()->company_id)
            ->where('acciones_detalle_valores.acciones_detalle_id', $detalle)
            ->whereIn('acciones_valores.operacion', ['S', 'R'])
            ->first();
        $total = $resultado->resultado;
        return $total;
    }

    public static function verTotalFila($historial, $header)
    {
        $resultado = AccionesDetalleValores::where('company_id', Auth::user()->company_id)
            ->where('acciones_header_id', $header)
            ->where('acciones_valores_id', $historial)
            ->sum('valor');
        return number_format($resultado, 2);
    }

    public static function verTotal($header)
    {
        $resultado = AccionesDetalleValores::select(DB::raw('SUM(CASE WHEN acciones_valores.operacion = "S" THEN acciones_detalle_valores.valor ELSE 0 END) - SUM(CASE WHEN acciones_valores.operacion = "R" THEN acciones_detalle_valores.valor ELSE 0 END) AS resultado'))
            ->join('acciones_valores', 'acciones_detalle_valores.acciones_valores_id', '=', 'acciones_valores.id')
            ->join('acciones_detalle', 'acciones_detalle_valores.acciones_detalle_id', '=', 'acciones_detalle.id')
            ->where('acciones_detalle.company_id', Auth::user()->company_id)
            ->where('acciones_detalle_valores.acciones_header_id', $header)
            ->whereIn('acciones_valores.operacion', ['S', 'R'])
            ->first();
        $total = $resultado->resultado;
        return $total;
    }
    
    public function verEntrega($id){
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $detalle = AccionesDetalle::find($id);
        $customer  = Customer::find($detalle->customer_id);
        $accionesValores = AccionesValores::where('company_id', Auth::user()->company_id)
                ->where('status', 1)
                ->get();
        $valorIngresos = 0 ;
        $valorEgresos = 0 ;
        foreach ($accionesValores as $value) {
            $detalleValores = AccionesDetalleValores::where('acciones_header_id',$detalle->acciones_header_id)->where('acciones_detalle_id',$detalle->id)->where('acciones_valores_id', $value->id)->first();
            $value->valorMostrar = $detalleValores->valor;
            if($value->operacion == 'S'){
                $valorIngresos = $valorIngresos + $detalleValores->valor ;
            }else{
                $valorEgresos = $valorEgresos + $detalleValores->valor;
            }
        }
        $data['usuarioEntrega'] = User::find($detalle->user_entrega_id);
        $data['company'] = $company;
        $data['customer'] = $customer;
        $data['detalle'] = $detalle;
        $data['valorIngresos'] = $valorIngresos;
        $data['valorEgresos'] = $valorEgresos;
        $data['accionesValores'] = $accionesValores;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        return PDF::loadView('acciones-detalle.pdf_entrega', compact('data'))
            ->setPaper('A4', "portrait")
            ->download('entrega de valores '.$customer->nombres.' '.$customer->apellidos.' .pdf');
    }

    public function verCertificado($id){
        $company = Company::find(Auth::user()->company_id);
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $detalle = AccionesDetalle::find($id);
        $customer  = Customer::find($detalle->customer_id);
        $accionesValores = AccionesValores::where('company_id', Auth::user()->company_id)
                ->where('status', 1)
                ->get();
        $valorIngresos = 0 ;
        $valorEgresos = 0 ;
        foreach ($accionesValores as $value) {
            $detalleValores = AccionesDetalleValores::where('acciones_header_id',$detalle->acciones_header_id)->where('acciones_detalle_id',$detalle->id)->where('acciones_valores_id', $value->id)->first();
            $value->valorMostrar = $detalleValores->valor;
            if($value->operacion == 'S'){
                $valorIngresos = $valorIngresos + $detalleValores->valor ;
            }else{
                $valorEgresos = $valorEgresos + $detalleValores->valor;
            }
        }
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['company'] = $company;
        $data['detalle'] = $detalle;
        $data['customer'] = $customer;
        $data['total'] = $valorIngresos-$valorEgresos ;
        return PDF::loadView('acciones-detalle.pdf_certificado_aportacion', compact('data'))
        ->setPaper('A4', "landscape")
        ->download('certificado_aportacion.pdf');
    }
    
}
