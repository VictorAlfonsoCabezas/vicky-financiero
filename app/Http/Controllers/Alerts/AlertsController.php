<?php

namespace App\Http\Controllers\Alerts;

use App\Http\Controllers\Controller;
use App\Models\Alerts;
use App\Models\CreditFolderHeader;
use App\Models\CreditFolderDetail;
use App\Http\Controllers\Credit\CreditController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Response;
use App\User;

class AlertsController extends Controller {

    public function consultarFechas() {
        $creditDetail = CreditFolderDetail::select('id', 'code_folder_header', 'date_vencimiento', 'numero_cuota')
                ->where('company_id', Auth::user()->company_id)
                ->where('date_vencimiento', '<', date('Y-m-d'))
                ->where('status', 'PENDIENTE');
        foreach ($creditDetail->get() as $value) {
            $alerts = Alerts::where('company_id', Auth::user()->company_id)->where('credit_detail_id', $value->id);
            if ($alerts->count() == 0) {
                $nombre = CreditFolderHeader::where('code', $value->code_folder_header)->first();
                $alerta = AlertsController::crearAlertas('LETRA VENCIDA', $nombre->customer_name, $value->id, $value->date_vencimiento, $value->numero_cuota);
            }
        }
        $alertas['numero'] = $creditDetail->count();
        return Response::json($alertas);
    }

    public static function crearAlertas($tipo, $nombre, $detail_id, $fecha, $numero) {
        $data = [
            "company_id" => Auth::user()->company_id,
            "type" => $tipo,
            "name" => $nombre,
            "description" => '<b class="text-danger">' . $tipo . '</b><br>  de: ' . $nombre . '<br><small class="badge badge-danger">' . $fecha . '</small>',
            "credit_detail_id" => $detail_id,
        ];
        Alerts::create($data);
    }

    public function consultarNuevas() {
        $alertas = Alerts::where('company_id', Auth::user()->company_id)
                        ->where('estatus_view', 'NUEVA')->get();
        return Response::json($alertas);
    }

    public function quitarAlerta($id) {
        $alerta = Alerts::find($id);
        $alerta->estatus_view = 'REVISADO';
        $alerta->save();
        return Response::json($alerta->id);
    }

    public function irPrestamos($id) {
        $alert = Alerts::find($id);
        $detalleCedito = CreditFolderDetail::find($alert->credit_detail_id);
        $creditHeader = CreditFolderHeader::where('code', $detalleCedito->code_folder_header)->first();
        return Response::json($creditHeader->customer_id);
    }

}
