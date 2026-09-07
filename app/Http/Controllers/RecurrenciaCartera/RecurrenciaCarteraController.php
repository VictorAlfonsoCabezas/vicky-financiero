<?php

namespace App\Http\Controllers\RecurrenciaCartera;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecurrenciaCartera;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\CarteraHeader;
use App\Models\CarteraDetail;
use Carbon\Carbon;
use App\Models\CreditFolderDetail;
use App\Models\CreditFolderHeader;

class RecurrenciaCarteraController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $datos = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->orderBy('orden', 'asc')->get();
        return view('recurrencia-cartera/index')->with('datos', $datos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $company = Company::find(Auth::user()->company_id);
        $dias = ($request->input('hasta') != null && $request->input('hasta') != '') ? $request->input('hasta') - $request->input('desde') + 1 : null;
        $data = [
            'company_id' => $company->id,
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
            'dias' => $dias,
            'orden' => $request->input('orden'),
            'date_created' => date('Y-m-d'),
            'hour_created' => date('H:i:s'),
            'user_created_id' => Auth::user()->id,
            'user_created_name' => Auth::user()->username,
        ];
        $existe = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->where('desde', $request->input('desde'))->where('hasta', $request->input('hasta'))->where('dias', $dias);
        if ($existe->count() == 0) {
            RecurrenciaCartera::create($data);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($heder, $recu) {
        $header = CarteraHeader::where('company_id', Auth::user()->company_id)->findOrFail($heder);
        $recurrencia = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->findOrFail($recu);
        if ($header->tipo == 'P') {
            $date = date('Y-m-d');
//        fecha inicial 
            $fecha = Carbon::parse($date);
            $nuevaFechaInicial = $fecha->addDays(($recurrencia->desde - 1))->format('Y-m-d');
            if ($recurrencia->hasta != null) {
//            fecha final
                $fechaFin = Carbon::parse($date);
                $nuevaFechaFin = $fechaFin->addDays($recurrencia->hasta)->format('Y-m-d');
                $detalles = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('status', 'PENDIENTE')->where('date_vencimiento', '>=', $nuevaFechaInicial)->where('date_vencimiento', '<=', $nuevaFechaFin)->orderBy('date_vencimiento', 'asc')->get();
            } else {
                $detalles = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('status', 'PENDIENTE')->where('date_vencimiento', '>=', $nuevaFechaInicial)->orderBy('date_vencimiento', 'asc')->get();
            }
        } else {
//        ayer
            $date = date('Y-m-d');
//            fecha inicial
            $fecha = Carbon::parse($date);
            $nuevaFechaInicial = $fecha->subDays($recurrencia->desde)->format('Y-m-d');
            if ($recurrencia->hasta != null) {
//                fecha hasta
                $fechaFin = Carbon::parse($nuevaFechaInicial);
                $nuevaFechaFin = $fechaFin->subDays($recurrencia->hasta)->format('Y-m-d');
                $detalles = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('status', 'PENDIENTE')->where('date_vencimiento', '<=', $nuevaFechaInicial)->where('date_vencimiento', '>=', $nuevaFechaFin)->orderBy('date_vencimiento', 'desc')->get();
            } else {
                $detalles = CreditFolderDetail::where('company_id', Auth::user()->company_id)->where('status', 'PENDIENTE')->where('date_vencimiento', '<=', $nuevaFechaInicial)->orderBy('date_vencimiento', 'desc')->get();
            }
        }
        foreach ($detalles as $value) {
            $credit = CreditFolderHeader::where('company_id', Auth::user()->company_id)->where('code', $value->code_folder_header)->first();
            $value->nombresClinetes = $credit ? $credit->customer_name : 'Cliente no disponible';
        }
        $texto = ($header->tipo == 'P') ? 'Letras Pendientes de pagos' : 'Letras Vencidas de Pagos';
        return view('recurrencia-cartera/show')
                        ->with('texto', $texto)
                        ->with('detalles', $detalles);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $rec = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $rec->delete();
        return redirect('recurrenciaCartera')
                        ->with('mensaje', 'Compania Eliminada Satisfactoriamente...');
    }

    public function recurrenciaCarteraVew() {
        $datosPendientes = CarteraHeader::where('company_id', Auth::user()->company_id)
                ->where('tipo', 'P')
                ->orderBy('mes', 'asc')
                ->get();

        $datosVencidos = CarteraHeader::where('company_id', Auth::user()->company_id)
                ->where('tipo', 'V')
                ->orderBy('mes', 'asc')
                ->get();

        return view('recurrencia-cartera/vew')
                        ->with('datosPendientes', $datosPendientes)
                        ->with('datosVencidos', $datosVencidos);
    }

    public static function datosRcurrente() {
        $datos = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->orderBy('orden', 'asc')->get();
        return $datos;
    }

    public static function datosTabla($id) {
        $datos = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->orderBy('orden', 'asc')->get();
        foreach ($datos as $value) {
            $valores = CarteraDetail::where('cartera_header_id', $id)->where('recurrencia_cartera_id', $value->id)->sum('valores');
            $value->sumatoria = $valores;
        }
        return $datos;
    }

    public function calcular() {
        $datos = RecurrenciaCartera::where('company_id', Auth::user()->company_id)->orderBy('orden', 'asc')->get();
        $mes = intval(date('m'));
        $existeheaderPendiente = CarteraHeader::where('company_id', Auth::user()->company_id)->where('mes', $mes)->where('year', date('Y'))->where('tipo', 'P');
        if ($existeheaderPendiente->count() == 0) {
            $headerPendiente = [
                'company_id' => Auth::user()->company_id,
                'mes' => $mes,
                'mes_name' => strtoupper(Carbon::create(null, $mes)->locale('es')->monthName),
                'year' => date('Y'),
                'tipo' => 'P',
                'date_save' => date('Y-m-d'),
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $headerP = CarteraHeader::create($headerPendiente);
        } else {
            $headerP = $existeheaderPendiente->first();
            $headerP->date_save = date('Y-m-d');
            $headerP->hour_created = date('H:i:s');
            $headerP->save();
        }
        $date = date('Y-m-d');
        foreach ($datos as $val) {
            if ($val->dias != null) {
                $fecha = Carbon::parse($date);
                $nuevaFecha = $fecha->addDays($val->dias)->format('Y-m-d');
                $cobrar = CreditFolderDetail::where('status', 'PENDIENTE')->where('date_vencimiento', '>=', $date)->where('date_vencimiento', '<=', $nuevaFecha)->sum('capital_amortizado');
                $fecha2 = Carbon::parse($date);
                $date = $fecha2->addDays(($val->hasta + 1))->format('Y-m-d');
            } else {
                $cobrar = CreditFolderDetail::where('status', 'PENDIENTE')->where('date_vencimiento', '>=', $date)->sum('capital_amortizado');
            }


            $data = [
                'cartera_header_id' => $headerP->id,
                'recurrencia_cartera_id' => $val->id,
                'valores' => $cobrar,
                'date_save' => date('Y-m-d'),
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $existedata = CarteraDetail::where('cartera_header_id', $headerP->id)->where('recurrencia_cartera_id', $val->id);
            if ($existedata->count() == 0) {
                CarteraDetail::create($data);
            } else {
                $update = $existedata->first();
                $update->valores = $cobrar;
                $update->date_save = date('Y-m-d');
                $update->save();
            }
        }
        $existeheaderPendiente = CarteraHeader::where('company_id', Auth::user()->company_id)->where('mes', $mes)->where('year', date('Y'))->where('tipo', 'V');
        if ($existeheaderPendiente->count() == 0) {
            $headerPendiente = [
                'company_id' => Auth::user()->company_id,
                'mes' => $mes,
                'mes_name' => strtoupper(Carbon::create(null, $mes)->locale('es')->monthName),
                'year' => date('Y'),
                'tipo' => 'V',
                'date_save' => date('Y-m-d'),
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $headerP = CarteraHeader::create($headerPendiente);
        } else {
            $headerP = $existeheaderPendiente->first();
            $headerP->date_save = date('Y-m-d');
            $headerP->hour_created = date('H:i:s');
            $headerP->save();
        }
//        ayer
        $fechaHoy = date('Y-m-d');
        $fecha = Carbon::parse($fechaHoy);
        $nuevaFecha = $fecha->subDays(1)->format('Y-m-d');
        $date = $nuevaFecha;
        foreach ($datos as $val) {
            if ($val->dias != null) {
                $fecha = Carbon::parse($date);
                $nuevaFecha = $fecha->subDays($val->dias)->format('Y-m-d');
                $cobrar = CreditFolderDetail::where('status', 'PENDIENTE')->where('date_vencimiento', '<=', $date)->where('date_vencimiento', '>=', $nuevaFecha)->sum('capital_amortizado');
                $fecha2 = Carbon::parse($date);
                $date = $fecha2->subDays(($val->hasta + 1))->format('Y-m-d');
            } else {
                $cobrar = CreditFolderDetail::where('status', 'PENDIENTE')->where('date_vencimiento', '<=', $date)->sum('capital_amortizado');
            }
            $data = [
                'cartera_header_id' => $headerP->id,
                'recurrencia_cartera_id' => $val->id,
                'valores' => $cobrar,
                'date_save' => date('Y-m-d'),
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created_id' => Auth::user()->id,
                'user_created_name' => Auth::user()->username,
            ];
            $existedata = CarteraDetail::where('cartera_header_id', $headerP->id)->where('recurrencia_cartera_id', $val->id);
            if ($existedata->count() == 0) {
                CarteraDetail::create($data);
            } else {
                $update = $existedata->first();
                $update->valores = $cobrar;
                $update->date_save = date('Y-m-d');
                $update->save();
            }
        }
    }

}
