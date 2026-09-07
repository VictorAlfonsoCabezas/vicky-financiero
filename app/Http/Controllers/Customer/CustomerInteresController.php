<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerHistorial;
use App\Models\CustomerMovimiento;
use App\Models\CustomerInteres;
use App\Models\FondoDetail;
use App\Models\TypeTransaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use App\Exports\MovimientosDiarios;

class CustomerInteresController extends Controller {

    public function index() {
        $intereses = CustomerInteres::all();
        return view('interes/index')->with('intereses', $intereses);
    }

    public function historia() {
        $historial = CustomerMovimiento::orderBy('id', 'desc')->get();
        return view('interes/historia')
                        ->with('historial', $historial);
    }

    public function descargarHistoria() {
        return Excel::download(new MovimientosDiarios, 'Movimientos.xlsx');
    }

    public static function nombreCliente($code) {
        $customer = Customer::where('code', $code)->first();
        $nombre = $customer->nombres . ' ' . $customer->apellidos;
        return $nombre;
    }

    public function verTabla($id) {
        $historial = FondoDetail::where('type_transaction_id', $id)->get();
        return Response::json($historial);
    }

}
