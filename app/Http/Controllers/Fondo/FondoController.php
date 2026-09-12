<?php

namespace App\Http\Controllers\Fondo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FondoHeader;
use App\Models\FondoDetail;
use App\Models\TypeTransaction;
use Illuminate\Support\Facades\Auth;
use Response;

class FondoController extends Controller {

    public function index() {
        $fondos = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true)->get();
        return view('fondo/index')
                        ->with('fondos', $fondos);
    }

    public function storeTransacciones(Request $request) {
        $request->validate(['fondo_id' => 'nullable|integer|min:1', 'type_fondo' => 'required|in:IN,EG',
            'valor_fondo' => 'required|numeric|min:0.01', 'observation' => 'required|string|max:1000']);
        $query = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true);
        $fondos = $request->filled('fondo_id') ? $query->findOrFail($request->input('fondo_id')) : $query->firstOrFail();
        if ($request->input('type_fondo') == 'IN') {
            $transaction = TypeTransaction::where('name_corto', 'IN')->first();
            $fondoUpdate = FondoHeader::find($fondos->id);

            $valInicial = $fondoUpdate->valor_inicial;
            $valIgreso = $fondoUpdate->valor_ingreso;
            $valEgreso = $fondoUpdate->valor_egreso;
            $valTotal = $fondoUpdate->valor_total;

            $valIgresoNuevo = $valIgreso + $request->input('valor_fondo');
            $valTotalNuevo = $valIgresoNuevo + $valInicial - $valEgreso;
            $fondoUpdate->valor_ingreso = $valIgresoNuevo;
            $fondoUpdate->valor_total = $valTotalNuevo;
            $fondoUpdate->save();
            $data = [
                "code_header_id" => $fondoUpdate->code,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor" => $request->input('valor_fondo'),
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
                "observation_created" => $request->input('observation'),
            ];
            $detalle = FondoDetail::create($data);
            return Response::json(true);
        } else {
            $transaction = TypeTransaction::where('name_corto', 'EG')->first();
            $fondoUpdate = FondoHeader::find($fondos->id);
            $valInicial = $fondoUpdate->valor_inicial;
            $valIgreso = $fondoUpdate->valor_ingreso;
            $valEgreso = $fondoUpdate->valor_egreso;
            $valTotal = $fondoUpdate->valor_total;
            $valEgresoNuevo = $valEgreso + $request->input('valor_fondo');
            $valTotalNuevo = $valIgreso + $valInicial - $valEgresoNuevo;
            $fondoUpdate->valor_egreso = $valEgresoNuevo;
            $fondoUpdate->valor_total = $valTotalNuevo;
            $fondoUpdate->save();
            $data = [
                "code_header_id" => $fondoUpdate->code,
                "type_transaction_id" => $transaction->id,
                "type_transaction_name" => $transaction->name,
                "type_transaction_action" => $transaction->action,
                "valor" => $request->input('valor_fondo'),
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "date_created" => date('Y-m-d'),
                "hour_created" => date("H:i:s"),
                "observation_created" => $request->input('observation'),
            ];
            $detalle = FondoDetail::create($data);
            return Response::json(true);
        }
    }

    public static function transaccionesFondo($transaccion) {
        $fondos = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true)->first();
        if ($transaccion->type_transaction_action == 'S') {
            $fondoUpdate = FondoHeader::find($fondos->id);

            $valInicial = $fondoUpdate->valor_inicial;
            $valIgreso = $fondoUpdate->valor_ingreso;
            $valEgreso = $fondoUpdate->valor_egreso;
            $valTotal = $fondoUpdate->valor_total;

            $valIgresoNuevo = $valIgreso + $transaccion->valor_movimiento;
            $valTotalNuevo = $valIgresoNuevo + $valInicial - $valEgreso;
            $fondoUpdate->valor_ingreso = $valIgresoNuevo;
            $fondoUpdate->valor_total = $valTotalNuevo;
            $fondoUpdate->save();

            $data = [
                "code_header_id" => $fondoUpdate->code,
                "type_transaction_id" => $transaccion->type_transaction_id,
                "type_transaction_name" => $transaccion->type_transaction_name,
                "type_transaction_action" => $transaccion->type_transaction_action,
                "valor" => $transaccion->valor_movimiento,
                "saldo_general" => $valTotalNuevo,
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "date_created" => $transaccion->date_created,
                "hour_created" => $transaccion->hour_created,
                "observation_created" => 'DEPOSITO DE ' . $transaccion->customer_name,
            ];
            $detalle = FondoDetail::create($data);
        } else {
            $fondoUpdate = FondoHeader::find($fondos->id);

            $valInicial = $fondoUpdate->valor_inicial;
            $valIgreso = $fondoUpdate->valor_ingreso;
            $valEgreso = $fondoUpdate->valor_egreso;
            $valTotal = $fondoUpdate->valor_total;

            $valEgresoNuevo = $valEgreso + $transaccion->valor_movimiento;
            $valTotalNuevo = $valIgreso + $valInicial - $valEgresoNuevo;
            $fondoUpdate->valor_egreso = $valEgresoNuevo;
            $fondoUpdate->valor_total = $valTotalNuevo;
            $fondoUpdate->save();

            $data = [
                "code_header_id" => $fondoUpdate->code,
                "type_transaction_id" => $transaccion->type_transaction_id,
                "type_transaction_name" => $transaccion->type_transaction_name,
                "type_transaction_action" => $transaccion->type_transaction_action,
                "valor" => $transaccion->valor_movimiento,
                "user_created_id" => Auth::user()->id,
                "user_created_name" => Auth::user()->username,
                "date_created" => $transaccion->date_created,
                "hour_created" => $transaccion->hour_created,
                "observation_created" => 'RETIRO DE ' . $transaccion->customer_name,
            ];
            $detalle = FondoDetail::create($data);
        }
    }

    public static function entregarCredito($credito) {
        $fondos = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true)->first();

        $fondoUpdate = FondoHeader::find($fondos->id);

        $valInicial = $fondoUpdate->valor_inicial;
        $valIgreso = $fondoUpdate->valor_ingreso;
        $valEgreso = $fondoUpdate->valor_egreso;
        $valTotal = $fondoUpdate->valor_total;

        $valEgresoNuevo = $valEgreso + $credito->valor_solicitado;
        $valTotalNuevo = $valIgreso + $valInicial - $valEgresoNuevo;
        $fondoUpdate->valor_egreso = $valEgresoNuevo;
        $fondoUpdate->valor_total = $valTotalNuevo;
        $fondoUpdate->save();

        $transaction = TypeTransaction::where('name_corto', 'ENC')->first();

        $data = [
            "code_header_id" => $fondoUpdate->code,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor" => $credito->valor_solicitado,
            "user_created_id" => Auth::user()->id,
            "user_created_name" => Auth::user()->username,
            "date_created" => $credito->date_created,
            "hour_created" => $credito->date_created,
            "observation_created" => $transaction->name . ' ' . $credito->customer_name,
        ];
        $detalle = FondoDetail::create($data);
    }

    public static function pagoLetra($letra, $numero) {
        $fondos = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true)->first();
        $transaction = TypeTransaction::where('name_corto', 'PC')->first();
        $fondoUpdate = FondoHeader::find($fondos->id);

        $valInicial = $fondoUpdate->valor_inicial;
        $valIgreso = $fondoUpdate->valor_ingreso;
        $valEgreso = $fondoUpdate->valor_egreso;
        $valTotal = $fondoUpdate->valor_total;

        $valIgresoNuevo = $valIgreso + $letra->valor_movimiento;
        $valTotalNuevo = $valIgresoNuevo + $valInicial - $valEgreso;
        $fondoUpdate->valor_ingreso = $valIgresoNuevo;
        $fondoUpdate->valor_total = $valTotalNuevo;
        $fondoUpdate->save();
        $data = [
            "code_header_id" => $fondoUpdate->code,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor" => $letra->valor_movimiento,
            "user_created_id" => Auth::user()->id,
            "user_created_name" => Auth::user()->username,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "observation_created" => $transaction->name . ' # ' . $numero . ' de ' . $letra->customer_name,
        ];
        $detalle = FondoDetail::create($data);
    }

    public static function Incobrable($letra, $numero) {
        $fondos = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true)->first();
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'PCI')
                ->first();

        $fondoUpdate = FondoHeader::find($fondos->id);
        $valInicial = $fondoUpdate->valor_inicial;
        $valIgreso = $fondoUpdate->valor_ingreso;
        $valEgreso = $fondoUpdate->valor_egreso;
        $valTotal = $fondoUpdate->valor_total;

        $valIgresoNuevo = $valIgreso + $letra->valor_movimiento;
        $valTotalNuevo = $valIgresoNuevo + $valInicial - $valEgreso;
        $fondoUpdate->valor_ingreso = $valIgresoNuevo;
        $fondoUpdate->valor_total = $valTotalNuevo;
        $fondoUpdate->save();
        $data = [
            "code_header_id" => $fondoUpdate->code,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor" => $letra->valor_movimiento,
            "user_created_id" => Auth::user()->id,
            "user_created_name" => Auth::user()->username,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "observation_created" => $transaction->name . ' # ' . $numero . ' de ' . $letra->customer_name,
        ];
        $detalle = FondoDetail::create($data);
    }

    public static function valorEncaje($credito) {
        $fondos = FondoHeader::where('company_id', Auth::user()->company_id)->where('status', true)->first();
        $transaction = TypeTransaction::where('company_id', Auth::user()->company_id)
                ->where('name_corto', 'ENC')
                ->first();
        $fondoUpdate = FondoHeader::find($fondos->id);

        $valInicial = $fondoUpdate->valor_inicial;
        $valIgreso = $fondoUpdate->valor_ingreso;
        $valEgreso = $fondoUpdate->valor_egreso;
        $valTotal = $fondoUpdate->valor_total;

        $valIgresoNuevo = $valIgreso + $credito->valor_encaje;
        $valTotalNuevo = $valIgresoNuevo + $valInicial - $valEgreso;
        $fondoUpdate->valor_ingreso = $valIgresoNuevo;
        $fondoUpdate->valor_total = $valTotalNuevo;
        $fondoUpdate->save();
        $data = [
            "code_header_id" => $fondoUpdate->code,
            "type_transaction_id" => $transaction->id,
            "type_transaction_name" => $transaction->name,
            "type_transaction_action" => $transaction->action,
            "valor" => $credito->valor_encaje,
            "user_created_id" => Auth::user()->id,
            "user_created_name" => Auth::user()->username,
            "date_created" => date('Y-m-d'),
            "hour_created" => date("H:i:s"),
            "observation_created" => $transaction->name . ' del crédito a nombre de ' . $credito->customer_name,
        ];
        $detalle = FondoDetail::create($data);
    }

    public function verMovimientos($id) {
        $fondo = FondoHeader::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $code = $fondo->code;
        $detalles = FondoDetail::where('code_header_id', $code)->get();
        return Response::json($detalles);
    }

}
