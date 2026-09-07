<?php

namespace App\Http\Controllers\Cartola;

use App\Http\Controllers\Controller;
use App\Models\CartolaHeader;
use App\Models\CartolaDetail;
use App\Http\Controllers\Base\BaseController;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\TipoAhorrosDetalle;
use Illuminate\Support\Facades\Auth;
use App\Models\TypeTransaction;
use Illuminate\Http\Request;
use Response;
use PDF;

class CartolaController extends Controller
{

    public static function ingresoCartola($userCode, $tipoTransaccion, $valor, $fecha)
    {
        $cartola = CartolaHeader::where('customer_code', $userCode)->where('status', 'ACTIVA')->first();
        $cartolaNumero = CartolaHeader::where('customer_code', $userCode)->where('status', 'ACTIVA')->get();
        $cantidad = count($cartolaNumero);
        $customer = Customer::where('code', $userCode)->first();
        $transaccion = TypeTransaction::find($tipoTransaccion);
        if ($cantidad > 0) {
            $cartolsDetalle = CartolaDetail::where('cartola_header_code', $cartola->code);
            if ($cartolsDetalle->count() <= 40) {
                $data = [
                    "cartola_header_code" => $cartola->code,
                    "type_transaction_id" => $transaccion->id,
                    "type_transaction_name" => $transaccion->name,
                    "type_transaction_action" => $transaccion->action,
                    "valor_transaction" => $valor,
                    "date_transaction" => $fecha,
                ];
                $cartolaDetail = CartolaDetail::create($data);
                $suma = CartolaDetail::where('cartola_header_code', $cartola->code)->where('type_transaction_action', 'S')->sum('valor_transaction');
                $resta = CartolaDetail::where('cartola_header_code', $cartola->code)->where('type_transaction_action', 'R')->sum('valor_transaction');
                $diferencia = $suma - $resta;
                $cartolaDetail->saldo_transaction = $diferencia;
                $cartolaDetail->save();
            } else {
                $cartolaUpdate = CartolaHeader::find($cartola->id);
                $cartolaUpdate->status = 'CERRADA';
                $cartolaUpdate->save();
                $tabla = 'cartola_headers';
                $code = BaseController::generarCodigo($tabla, 3);
                $cartolaNew = [
                    "code" => $code,
                    "company_id" => Auth::user()->company_id,
                    "customer_id" => $customer->id,
                    "customer_code" => $customer->code,
                    "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                    "customer_date_create" => $fecha,
                    "status" => 'ACTIVA',
                ];
                $cartolanueva = CartolaHeader::create($cartolaNew);
                $cartolaDetalle = [
                    "cartola_header_code" => $cartolanueva->code,
                    "type_transaction_id" => $transaccion->id,
                    "type_transaction_name" => $transaccion->name,
                    "type_transaction_action" => $transaccion->action,
                    "valor_transaction" => $valor,
                    "date_transaction" => $fecha,
                ];
                $cartolaDetail = CartolaDetail::create($cartolaDetalle);
                $suma = CartolaDetail::where('cartola_header_code', $cartolanueva->code)->where('type_transaction_action', 'S')->sum('valor_transaction');
                $resta = CartolaDetail::where('cartola_header_code', $cartolanueva->code)->where('type_transaction_action', 'R')->sum('valor_transaction');
                $diferencia = $suma - $resta;
                $cartolaDetail->saldo_transaction = $diferencia;
                $cartolaDetail->save();
            }
        } else {
            $tabla = 'cartola_headers';
            $code = BaseController::generarCodigo($tabla, 3);
            $cartolaNew = [
                "code" => $code,
                "company_id" => Auth::user()->company_id,
                "customer_id" => $customer->id,
                "customer_code" => $customer->code,
                "customer_name" => $customer->nombres . ' ' . $customer->apellidos,
                "customer_date_create" => $fecha,
                "status" => 'ACTIVA',
            ];
            $cartolanueva = CartolaHeader::create($cartolaNew);
            $cartolaDetalle = [
                "cartola_header_code" => $cartolanueva->code,
                "type_transaction_id" => $transaccion->id,
                "type_transaction_name" => $transaccion->name,
                "type_transaction_action" => $transaccion->action,
                "valor_transaction" => $valor,
                "date_transaction" => $fecha,
            ];
            $cartolaDetail = CartolaDetail::create($cartolaDetalle);
            $suma = CartolaDetail::where('cartola_header_code', $cartolanueva->code)->where('type_transaction_action', 'S')->sum('valor_transaction');
            $resta = CartolaDetail::where('cartola_header_code', $cartolanueva->code)->where('type_transaction_action', 'R')->sum('valor_transaction');
            $diferencia = $suma - $resta;
            $cartolaDetail->saldo_transaction = $diferencia;
            $cartolaDetail->save();
        }
        return Response::json(true);
    }

    public function descargarPdf($id, $cuenta)
    {
        $company = Company::find(Auth::user()->company_id);
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
        $path2 = 'codev/negro.png';
        $data['cartolaHeader'] = CartolaHeader::where('customer_id', $id)->first();
        $data['customer'] = Customer::find($id);
        $detalle = CustomerMovimiento::select('customer_movimientos.*', 'type_transactions.name_corto')
            ->join('customer_tipo_ahorros', 'customer_movimientos.customer_tipo_ahorro_id', '=', 'customer_tipo_ahorros.id')
            ->join('customer', 'customer_tipo_ahorros.customer_id', '=', 'customer.id')
            ->join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
            ->where('customer_tipo_ahorros.company_id', Auth::user()->company_id)
            ->where('customer_tipo_ahorros.id', $cuenta)
            ->whereIn('type_transactions.name_corto', ['IN', 'EG', 'SE', 'SC', 'DEA', 'SOL', 'CVN', 'PVP', 'IFN', 'DCD', 'DFJ', 'DNC', 'DND', 'DCT', 'TRR', 'TRE', 'APS'])
            //->whereIn('type_transaction_id', [1, 2, 20, 21, 40,41])
            ->where('customer.id', $id)
            // ->where('customer_tipo_ahorro_id', $this->cuenta_selec)
            ->where('customer_movimientos.status', true)
            ->orderBy('id', 'asc')
            ->get();

        foreach($detalle  as $value){
            $value->imprimir = true;
            $tipoAhorroDetalle = TipoAhorrosDetalle::find($value->tipo_ahorro_detalle_id);
            if($tipoAhorroDetalle){
                if($tipoAhorroDetalle->suma){
                    $value->imprimir = true;
                }else{
                    $value->imprimir = false;
                }
            }
        }

        $data['detalleCartola'] = $detalle;
        $pdf = PDF::loadView('reportes.cartola-new', compact('data'))
            ->setPaper('A5', "portrait");
        $pdfName = 'Cartola N° : ' . $data['customer']->id . '.pdf';

        // Devuelve el PDF para que se descargue en el navegador
        return $pdf->download($pdfName);
    }
}
