<?php

namespace App\Http\Controllers\Asientos;

use App\Http\Controllers\Controller;
use App\Models\AsientosDetalle;
use App\Models\AsientosHeader;
use App\Models\Company;
use App\Models\Conceptos;
use App\Models\TipoConcepto;
use App\User;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Luecano\NumeroALetras\NumeroALetras;


class AsientosController extends Controller
{
    public function index()
    {
        return view('asientos/index');
    }

    public function comprobante($id)
    {
        $company = Company::find(Auth::user()->company_id);
        $header = AsientosHeader::find($id);
        $concepto = Conceptos::find($header->concepto_id);
        $tipoconcepto = TipoConcepto::find($concepto->tipo_concepto_id);
        $detalle = AsientosDetalle::select('asientos_detalle.*', 'plan_cuentas.codigo', 'plan_cuentas.nombre','centro_costos.name as centro_costo','sedes.name as sedes_name')
            ->join('plan_cuentas', 'asientos_detalle.plan_cuentas_id', '=', 'plan_cuentas.id')
            ->leftjoin('sedes_centro_costos', 'asientos_detalle.sedes_centro_costos_id', '=', 'sedes_centro_costos.id')
            ->leftjoin('centro_costos', 'sedes_centro_costos.centro_costos_id', '=', 'centro_costos.id')
            ->leftjoin('sedes', 'sedes_centro_costos.sede_id', '=', 'sedes.id')
            ->where('asientos_detalle.company_id', Auth::user()->company_id)
            ->where('asientos_detalle.asientos_header_id', $id)
            ->orderBy('asientos_detalle.debe_haber', 'DESC')
            ->orderBy('asientos_detalle.id')
            ->get();
        if ($company->photo != null && $company->photo != '') {
            $path = 'uploads/companies/' . $company->photo;
            if (!file_exists(public_path($path))) {
                $path = 'codev/negro.png';
            }
        } else {
            $path = 'codev/negro.png';
        }
        $data['company'] = $company;
        $man = file_get_contents(public_path($path));
        $data['imagen'] = base64_encode($man);
        $data['header'] = $header;
        $data['fechaActual'] = AsientosController::convertirFecha(date('Y-m-d'));
        $data['detalle'] = $detalle;
        $data['concepto'] = $concepto;
        $data['tipoconcepto'] = $tipoconcepto;
        $userElaborado = User::find($header->user_created);
        $data['elaborado_por'] = $userElaborado->firstname . ' ' . $userElaborado->lastname;
        $data['elaborado_ruc'] = $userElaborado->ruc;
        $userRecibe = User::find(Auth::user()->id);
        $data['recibido_por'] = $userRecibe->firstname . ' ' . $userRecibe->lastname;
        $data['recibido_ruc'] = $userRecibe->ruc;


        $formatter = new NumeroALetras();
        $sumaDebe = AsientosDetalle::where('asientos_detalle.company_id', Auth::user()->company_id)
            ->where('asientos_detalle.asientos_header_id', $id)
            ->where('asientos_detalle.debe_haber', true)
            ->sum('valor');
        $sumaHaber = AsientosDetalle::where('asientos_detalle.company_id', Auth::user()->company_id)
            ->where('asientos_detalle.asientos_header_id', $id)
            ->where('asientos_detalle.debe_haber', false)
            ->sum('valor');
        $data['cantidad_texto'] = $formatter->toMoney($sumaDebe, 2, 'DÓLARES', 'CENTAVOS');
        $data['debe'] = $sumaDebe;
        $data['haber'] = $sumaHaber;
        return PDF::loadView('asientos._pdf_asientos', compact('data'))
            ->setPaper('A4', "portrait")
            ->download('Asientos' . $header->id . '.pdf');
    }

    public static function convertirFecha($fecha)
    {
        $carbonFecha = Carbon::createFromFormat('Y-m-d', $fecha);
        $carbonFecha->locale('es');
        $fechaFormateada = $carbonFecha->isoFormat('dddd, D [de] MMMM [del] YYYY');
        return $fechaFormateada;
    }
}
