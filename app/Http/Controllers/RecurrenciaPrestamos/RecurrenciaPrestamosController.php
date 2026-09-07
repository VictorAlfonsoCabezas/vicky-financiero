<?php

namespace App\Http\Controllers\RecurrenciaPrestamos;

use App\Http\Controllers\Controller;
use App\Models\RecurrenciaPrestamos;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Response;

class RecurrenciaPrestamosController extends Controller {

    public function index() {
        $recurrencia = RecurrenciaPrestamos::where('company_id', Auth::user()->company_id)->get();
        return view('recurencias-prestamos/index')->with('recurrencia', $recurrencia);
    }

    public function save(Request $request) {
        if ($request->input('id_recu_pres') != null) {
            $recu = RecurrenciaPrestamos::find($request->input('id_recu_pres'));
            $recu->name = mb_strtoupper($request->input('name'));
            $recu->separacion = $request->input('separacion');
            $recu->code = $request->input('code');
            $recu->save();
        } else {
            $company = Company::find(Auth::user()->company_id);
            $data = [
                'company_id' => $company->id,
                'name' => mb_strtoupper($request->input('name')),
                'separacion' => $request->input('separacion'),
                'code' => $request->input('code'),
                'date_created' => date('Y-m-d'),
                'hour_created' => date('H:i:s'),
                'user_created' => Auth::user()->username,
                'user_id' => Auth::user()->id,
                'status' => 1,
            ];
            $existe = RecurrenciaPrestamos::where('separacion', $request->input('separacion'))->where('code', $request->input('code'));
            if ($existe->count() == 0) {
                RecurrenciaPrestamos::create($data);
            } else {
                $prestamo = $existe->first();
                $prestamo->name = mb_strtoupper($request->input('name'));
                $prestamo->separacion = $request->input('separacion');
                $prestamo->code = $request->input('code');
                $prestamo->save();
            }
        }

        return Response::json(true);
    }

    public function editar($id) {
        $recu = RecurrenciaPrestamos::find($id);
        return Response::json($recu);
    }

}
