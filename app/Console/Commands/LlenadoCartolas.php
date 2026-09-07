<?php

namespace App\Console\Commands;

use App\Models\CustomerTipoAhorros;
use App\Models\Customer;
use App\Models\CustomerMovimiento;
use App\Models\CartolaHeader;
use App\Models\CartolaDetail;
use App\Models\Company;
use App\Models\TypeTransaction;
use App\Http\Controllers\Base\BaseController;
use Illuminate\Console\Command;

class LlenadoCartolas extends Command
{

    protected $signature = 'command:llenarCartolas';
    protected $description = 'se correra una vezz para igualar las cartolas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $customerTipoAhorros = CustomerTipoAhorros::where('status', 1)->get();
        $company = Company::find(1);
        //dd('alto');

        foreach ($customerTipoAhorros as $value) {
            $code = '';
            $clientes = Customer::find($value->customer_id);
            $valorTotal = 0;
            if ($clientes) {
                $detalle = CustomerMovimiento::select('customer_movimientos.*', 'customer_tipo_ahorros.codigo', 'tipo_ahorros_detalle.siglas', 'type_transactions.nombre_cartola')
                    ->join('type_transactions', 'customer_movimientos.type_transaction_id', '=', 'type_transactions.id')
                    ->join('customer_tipo_ahorros', 'customer_movimientos.customer_tipo_ahorro_id', '=', 'customer_tipo_ahorros.id')
                    ->leftjoin('tipo_ahorros_detalle', 'customer_movimientos.tipo_ahorro_detalle_id', '=', 'tipo_ahorros_detalle.id')
                    ->where('customer_movimientos.customer_id', $clientes->id)
                    ->where('customer_movimientos.company_id', 1)
                    ->whereIn('type_transactions.name_corto', ['IN', 'EG', 'SE', 'SC', 'DEA', 'SOL', 'CVN', 'DNC', 'DND', 'PVP', 'IFN', 'DCD', 'DFJ'])
                    ->where('customer_movimientos.customer_tipo_ahorro_id', $value->id)
                    ->where('customer_movimientos.status', true)
                    ->orderBy('customer_movimientos.id', 'asc')
                    ->get();
                $tabla = 'cartola_headers';
                $code = BaseController::generarCodigoCron($tabla, 3);
                $cartolaNew = [
                    "code" => $code,
                    "numero" => $code,
                    "company_id" => 1,
                    "customer_id" => $clientes->id,
                    "customer_code" => $clientes->code,
                    "customer_name" => $clientes->nombres . ' ' . $clientes->apellidos,
                    "customer_date_create" => date('Y-m-d'),
                    "status" => 'ACTIVA',
                    "date_create" => date('Y-m-d'),
                    "hour_create" => date('H:i:s'),
                    "customer_tipo_ahorro_id" => $value->id,
                ];
                $cartolanueva = CartolaHeader::create($cartolaNew);
                echo 'SE CREA LA CARTOLA ' . $cartolanueva->id . ' .:.';
                $contadorA = 0;
                $finA = $company->cartola_a;
                $contadorB = 0;
                $finB = $company->cartola_b;
                $posicion = 0;
                foreach ($detalle as $key => $movi) {
                    if ($contadorA < $finA) {
                        $contadorA++;
                        $linea = $contadorA;
                        $cara = 'A';
                        $posicion = $contadorA;
                    } else if ($contadorB < $finB) {
                        $contadorB++;
                        $linea = $contadorB;
                        $cara = 'B';
                        $posicion = $contadorB;
                    } else {
                        $catolaOld = CartolaHeader::find($cartolanueva->id);
                        $catolaOld->status = 'CERRADA';
                        $catolaOld->save();
                        $tabla = 'cartola_headers';
                        $code = BaseController::generarCodigoCron($tabla, 3);
                        $cartolaNew = [
                            "code" => $code,
                            "numero" => $code,
                            "company_id" => 1,
                            "customer_id" => $clientes->id,
                            "customer_code" => $clientes->code,
                            "customer_name" => $clientes->nombres . ' ' . $clientes->apellidos,
                            "customer_date_create" => date('Y-m-d'),
                            "status" => 'ACTIVA',
                            "date_create" => date('Y-m-d'),
                            "hour_create" => date('H:i:s'),
                            "customer_tipo_ahorro_id" => $value->id,
                        ];
                        $cartolanueva = CartolaHeader::create($cartolaNew);
                        echo 'SE CREA LA CARTOLA ' . $cartolanueva->id . ' .:.';

                        $contadorA = 1;
                        $contadorB = 1;
                        $posicion = 1;
                        $linea = $contadorA;
                        $cara = 'A';
                    }
                    $transaccion = TypeTransaction::find($movi->type_transaction_id);
                    $cartolaDetalle = [
                        "cartola_header_code" => $cartolanueva->code,
                        "customer_movimientos_id" => $movi->id,
                        "type_transaction_id" => $transaccion->id,
                        "type_transaction_name" => $transaccion->name,
                        "type_transaction_action" => $transaccion->action,
                        "valor_transaction" => $movi->valor_movimiento,
                        "date_transaction" => $movi->date_created,
                        'cartola_headers_id' => $cartolanueva->id,
                        'cara' => $cara,
                        'posicion' => $posicion,
                    ];
                    $cartolaDetail = CartolaDetail::create($cartolaDetalle);

                    /*
                    $suma = CartolaDetail::where('cartola_headers_id', $cartolanueva->id)->where('type_transaction_action', 'S')->sum('valor_transaction');
                    $resta = CartolaDetail::where('cartola_headers_id', $cartolanueva->id)->where('type_transaction_action', 'R')->sum('valor_transaction');
                    $diferencia = $suma - $resta;
                    $cartolaDetail->saldo_transaction = $diferencia;
                    */
                    if ($transaccion->name_corto != 'SE') {

                        if ($transaccion->action == 'S') {
                            $valorTotal += $movi->valor_movimiento;
                        } else {
                            $valorTotal -= $movi->valor_movimiento;
                        }
                    }
                    $cartolaDetail->saldo_transaction = $valorTotal;
                    $cartolaDetail->save();
                    echo 'SE EL DETALLE ' . $cartolaDetail->id . ' DE LA CARTOLA ' . $cartolanueva->id . ' .:.';
                }
            }
        }
        $this->info('Llenado de cartolas finalizado.');
        return 0;
    }
}
